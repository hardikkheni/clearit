<?php

namespace App\Mail\Ticket;

use App\Repositories\Eloquent\TicketRepository;
use App\Services\TicketService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketNotify extends Mailable
{
    use Queueable, SerializesModels;

    protected $ticket;
    protected $ticketService;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(
        TicketService $ticketService,
        $ticket
    ) {
        $this->ticketService = $ticketService;
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
        $name = $this->ticketService->getFirstname($ticket) . ' ' . $this->ticketService->getLastname($ticket);
        $email = $this->ticketService->getEmail($ticket);
        return $this->from($config['from'], $config['fromName'])
            ->subject(sprintf('New %s is paid', $ticket['type']))
            ->text('emails.tickets.ticket_notify', [
                'ticketNumber' => TicketRepository::getTicketNumberFromId($ticket['id']),
                'paidDate' => $ticket['paidDate'],
                'name' => $name,
                'email' => $email
            ]);
    }
}
