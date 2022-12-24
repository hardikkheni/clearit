<?php

namespace App\Mail\Ticket;

use App\Services\TicketService;
use App\Repositories\Eloquent\TicketRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewTicketNotify extends Mailable
{
    use Queueable, SerializesModels;

    protected $ticket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        $ticket
    ) {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $config = config('constants.config.mail');
        $ticket = $this->ticket;
        $name = TicketService::getFirstname($ticket) . ' ' . TicketService::getLastname($ticket);
        $email = TicketService::getEmail($ticket);
        return $this->from($config['from'], $config['fromName'])
            ->subject('New ticket on ' . config('constants.config.SERVICE_NAME'))
            ->text('emails.tickets.new_ticket',[
                    'ticketid' => TicketRepository::getTicketNumberFromId($ticket['id']),
                    'name' => $name,
                    'email' => $email,
                    'service_name' => config('constants.config.SERVICE_NAME')
            ]);
    }
}
