<?php

namespace App\Console\Commands;

use App\Constants\Constant;
use App\Mail\OrderSyncFailedMail;
use App\Models\FailedOrderSync;
use App\Models\Order;
use App\Models\OrderLineItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SyncOrdersCommand extends Command
{
    protected $signature = 'orders:sync';

    public function handle()
    {
        $after    = Carbon::now()->subDays(30)->toIso8601String();
        $after    = substr($after, 0, -6).'Z'; // Remove milliseconds and append 'Z'
        $url      = config('woocommerce.api_url').'/orders?after='.urlencode($after);
        $response = Http::withBasicAuth(
            config('woocommerce.api_key'),
            config('woocommerce.api_secret')
        )->get($url);

        if ($response->successful()) {
            $orders    = $response->json();
            $batchSize = 100;

            $batches = array_chunk($orders, $batchSize);
            //syncing orders in batches
            foreach ($batches as $batch) {
                $this->syncBatch($batch);
            }

        } else {
            $this->error('Failed to fetch orders from WooCommerce API.');
        }
    }

    private function syncBatch($batch)
    {
        DB::transaction(function () use ($batch) {
            foreach ($batch as $orderData) {
                $this->syncOrder($orderData);
            }
        });
    }

    private function syncOrder($orderData)
    {
        try {
            $order = Order::updateOrCreate([
                'id' => $orderData['id'],
            ], [
                'number'        => $orderData['number'],
                'order_key'     => $orderData['order_key'],
                'status'        => $orderData['status'],
                'date_created'  => $orderData['date_created'],
                'total'         => $orderData['total'],
                'customer_id'   => $orderData['customer_id'],
                'customer_note' => $orderData['customer_note'],
                'billing'       => $orderData['billing'],
                'shipping'      => $orderData['shipping'],
            ]);

            foreach ($orderData['line_items'] as $lineItemData) {
                OrderLineItem::updateOrCreate([
                    'id' => $lineItemData['id'],
                ], [
                    'order_id' => $order->id,
                    'data'     => $lineItemData,
                ]);
            }
        } catch (\Exception $e) {
            FailedOrderSync::create([
                'order_data' => $orderData,
            ]);

            $this->error('Failed to sync order: '.$e->getMessage());
            Mail::to(Constant::DEVELOPER_EMAIL)
                ->send(new OrderSyncFailedMail($e->getMessage()));
        }
    }
}
