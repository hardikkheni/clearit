<?php

namespace App\Mail\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FreightosTariffCode extends Mailable
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
        return $this->from($config['from'], $config['fromName'])
            ->subject(sprintf('Tariff codes have been assigned to your FreightOS Shipment %s', $this->data['affiliateReferenceNumber']))
            ->view('emails.tickets.freightos_tariff_code')
            ->with($this->data);
    }
}
