<?php

namespace App\Mail\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTicketNoCharge extends Mailable
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
            ->subject('Your clearance request has been processed')
            ->view('emails.tickets.new_ticket_no_charge')
            ->with($this->data);
        if ($this->data['invoicePath']) {
            $build->attach($this->data['invoicePath']);
        }
        return $build;
    }
}
