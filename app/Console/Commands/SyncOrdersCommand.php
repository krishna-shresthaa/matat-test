<?php

namespace App\Console\Commands;

use App\Constants\Constant;
use App\Services\WooCommerceService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
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
            // Log the error
            Log::error('Failed to sync orders from WooCommerce API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Send an email notification
            $this->sendFailureNotificationEmail($e);

            return 1;
        }

        return 0;
    }

    /**
     * Send a notification email for a failed sync attempt.
     *
     * @param \Exception $exception
     * @return void
     */
    protected function sendFailureNotificationEmail($exception)
    {
        Mail::send('emails.failed_sync_notification', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ], function ($message) {
            $message->to(Constant::DEVELOPER_EMAIL)
                ->subject('Failed Order Sync Notification');
        });
    }
}
