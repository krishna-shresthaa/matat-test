<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldOrdersCommand extends Command
{
    protected $signature = 'orders:delete-old';

    public function handle()
    {
        $threshold = Carbon::now()->subMonths(3);
        //Delete all the orders which has not been updated in last 3 months along with orderItems
        Order::query()
            ->where('updated_at', '<', $threshold)
            ->with('lineItems')
            ->chunk(100, function ($orders) {
                foreach ($orders as $order) {
                    $order->lineItems()->delete();
                    $order->delete();
                }
            });


        $this->info('Old orders deleted successfully.');
    }
}
