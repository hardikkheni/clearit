<?php

namespace App\Mail\Affiliate;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AffiliateRegisteredLink extends Mailable
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
        $this->data['serviceName'] = str_replace(' ', '', config('constants.config.SERVICE_NAME'));
        $build = $this->from($config['from'], $config['fromName'])
            ->bcc($config['bcc'])
            ->subject(sprintf('Customs clearance for %s shipment #%s', $this->data['companyname'], $this->data['shipmentNumber']))
            ->markdown('emails.affiliate.registered_link')
            ->with($this->data);
        return $build;
    }
}
