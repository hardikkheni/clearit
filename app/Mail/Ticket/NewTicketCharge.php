<?php

namespace App\Mail\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTicketCharge extends Mailable
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
            ->subject('Your customs clearance has been processed')
            ->view('emails.tickets.new_ticket_charge')
            ->with($this->data);
        if ($this->data['invoicePath']) {
            $build->attach($this->data['invoicePath']);
        }
        return $build;
    }
}
