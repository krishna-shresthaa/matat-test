<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderLineItem;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class WooCommerceService
{
    /**
     * The WooCommerce API base URL.
     *
     * @var string
     */
    protected mixed $baseUrl;

    /**
     * The WooCommerce API consumer key.
     *
     * @var string
     */
    protected mixed $consumerKey;

    /**
     * The WooCommerce API consumer secret.
     *
     * @var string
     */
    protected mixed $consumerSecret;

    /**
     * Create a new WooCommerceService instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl        = config('woocommerce.api_url');
        $this->consumerKey    = config('woocommerce.api_key');
        $this->consumerSecret = config('woocommerce.api_secret');
    }

    /**
     * Fetch orders from the WooCommerce API.
     *
     * @return integer
     * @throws Exception
     */
    public function fetchAndSyncOrders(): int
    {
        $page        = 1;
        $perPage     = 10;
        $totalOrders = 0;
        $startDate   = Carbon::now()->subDays(30);
        $endDate     = Carbon::now();

//        fetch all the orders along with order_items for last 30 days in different request and sync to the
//        database we fetch 10 records per request
        do {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->get("{$this->baseUrl}/orders", [
                    'after'    => $startDate->toIso8601String(),
                    'before'   => $endDate->toIso8601String(),
                    'per_page' => $perPage,
                    'page'     => $page,
                ]);

            if ($response->successful()) {
                $orders = $response->json();

                foreach ($orders as $orderData) {
                    $totalOrders++;

                    $order = Order::updateOrCreate([
                        'number'    => $orderData['number'],
                        'order_key' => $orderData['order_key'],
                    ], [
                        'status'        => $orderData['status'],
                        'date_created'  => $orderData['date_created'],
                        'total'         => $orderData['total'],
                        'customer_id'   => $orderData['customer_id'] ?? null,
                        'customer_note' => $orderData['customer_note'] ?? null,
                        'billing'       => $orderData['billing'] ?? null,
                        'shipping'      => $orderData['shipping'] ?? null,
                    ]);

                    $this->syncOrderItems($order, $orderData['line_items']);
                }

                $page++;
            } else {
                // Handle API error
                throw new Exception($response->body());
            }
        } while ($orders && count($orders) === $perPage);

        return $totalOrders;
    }

    /**
     * Sync order items for the given order.
     *
     * @param Order $order
     * @param array $lineItems
     * @return void
     */
    protected function syncOrderItems(Order $order, array $lineItems)
    {
        foreach ($lineItems as $lineItem) {
            OrderLineItem::updateOrCreate([
                'order_id' => $order->id,
                'data->id' => $lineItem['id'],
            ], [
                'data' => $lineItem,
            ]);
        }
    }
}
