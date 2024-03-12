<?php

namespace App\Console\Commands;

use App\Models\FailedOrderSync;
use App\Models\Order;
use App\Models\OrderLineItem;
use App\Traits\SyncOrdersTrait;
use Illuminate\Console\Command;

class RetryFailedOrderSyncsCommand extends Command
{
    use SyncOrdersTrait;

    protected $signature = 'orders:retry-failed-syncs';

    public function handle()
    {
        $failedSyncs = FailedOrderSync::all();

        foreach ($failedSyncs as $failedSync) {
            try {
                $orderData = $failedSync->order_data;
                $this->syncOrder($orderData);
                $failedSync->delete();
            } catch (\Exception $e) {
                $this->error('Failed to retry order sync: '.$e->getMessage());
            }
        }

        $this->info('Retried failed order syncs.');
    }
}
