<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderSyncFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $errorMessage;

    public function __construct($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    public function build()
    {
        return $this->view('emails.order_sync_failed')
            ->subject('Order Sync Failed');
    }
}
