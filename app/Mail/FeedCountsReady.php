<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedCountsReady extends Mailable
{
    use Queueable, SerializesModels;

    private $webshopCount;

    /**
     * Create a new message instance.
     *
     * @param  array  $webshopCount
     */
    public function __construct(array $webshopCount)
    {
        $this->webshopCount = $webshopCount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('shops_report')->with(['websiteData' => $this->webshopCount]);
    }
}
