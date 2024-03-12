<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class DeleteOldOrdersCommand extends Command
{
    protected $signature = 'orders:delete-old';

    public function handle()
    {
        $threshold = Carbon::now()->subMonths(3);
        $orders = Order::where('updated_at', '<', $threshold)->get();

        foreach ($orders as $order) {
            $order->delete();
        }

        $this->info('Old orders deleted successfully.');
    }
}
