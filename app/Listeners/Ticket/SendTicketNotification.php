<?php

namespace App\Listeners\Ticket;

use App\Events\Ticket\TicketDocumentUploaded;
use App\Mail\Ticket\RoleDocumentUploaded;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendTicketNotification
{

    protected UserRepository $userRepo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserRepository $userRepo
    ) {
        $this->userRepo = $userRepo;
    }

    /**
     * Handle the event.
     *
     * @param  TicketDocumentUploaded  $event
     * @return void
     */
    public function handle(TicketDocumentUploaded $event)
    {
        $users = $this->userRepo->getUserByRoleBitMask(4);
        if (!count($users)) {
            Log::info("No user found with ISF role!");
            return false;
        }
        foreach ($users as $user) {
            // Mail::to($user['email'])
            Mail::to('sandeepd.test@gmail.com')
                ->send(new RoleDocumentUploaded([
                    'ticketId' => $event->ticket['id'],
                    'event' => $event->event,
                    'firstname' => $user['firstname'],
                    'url' => url("/ticket/master/{$event->ticket['guid']}")
                ]));
        }
    }
}
