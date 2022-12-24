<?php

namespace App\Mail\Affiliate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateCustomerContacted extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        array $data
    ) {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $config = config('constants.config.mail');
        $build = $this->from($config['from'], $config['fromName'])
            ->bcc($config['bcc'])
            ->subject(sprintf('Customer for ref #%s has been contacted', $this->data['shipmentNumber']))
            ->markdown('emails.affiliate.customer_contacted')
            ->with($this->data);
        return $build;
    }
}
