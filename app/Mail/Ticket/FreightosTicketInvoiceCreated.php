<?php

namespace App\Mail\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FreightosTicketInvoiceCreated extends Mailable
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
            ->bcc($config['bcc'])
            ->subject(
                sprintf(
                    ' A new invoice has been generated for %s, Ref #%s.',
                    $this->data['customerName'] ?? '',
                    $this->data['affiliateReferenceNumber'] ?? ''
                )
            )
            ->view('emails.tickets.freightos_ticket_invoice_created')
            ->with($this->data);
    }
}
