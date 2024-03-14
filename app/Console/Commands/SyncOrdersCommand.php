<?php

namespace App\Console\Commands;

use App\Constants\Constant;
use App\Exceptions\ApiException;
use App\Mail\OrderSyncFailedMail;
use App\Services\WooCommerceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SyncOrdersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync orders from WooCommerce API';

    /**
     * The WooCommerce service instance.
     *
     * @var WooCommerceService
     */
    protected WooCommerceService $wooCommerceService;

    /**
     * Create a new command instance.
     *
     * @param WooCommerceService $wooCommerceService
     * @return void
     */
    public function __construct(WooCommerceService $wooCommerceService)
    {
        parent::__construct();

        $this->wooCommerceService = $wooCommerceService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $totalOrders = $this->wooCommerceService->fetchAndSyncOrders();

            $this->info("Synced {$totalOrders} orders from WooCommerce API.");
        } catch (\Exception $e) {
            $this->error('Failed to sync order: '.$e->getMessage());
        }

        return 0;
    }
}
