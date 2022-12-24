<?php

namespace App\Mail\Ticket;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTicketPaid extends Mailable
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
            ->subject('Your clearance request has been processed')
            ->view('emails.tickets.new_ticket_paid')
            ->with($this->data);
    }
}
