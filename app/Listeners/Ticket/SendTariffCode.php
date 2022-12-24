<?php

namespace App\Listeners\Ticket;

use App\Events\Ticket\NotifyTariffCode;
use App\Mail\Ticket\FreightosTariffCode;
use App\Mail\Ticket\TariffCode;
use App\Repositories\Eloquent\AffiliateRepository;
use App\Repositories\Eloquent\TicketRepository;
use App\Repositories\Eloquent\UserHtsRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendTariffCode
{

    protected UserHtsRepository $uhtsRepo;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserHtsRepository $uhtsRepo
    ) {
        $this->uhtsRepo = $uhtsRepo;
    }

    /**
     * Handle the event.
     *
     * @param  NotifyTariffCode  $event
     * @return void
     */
    public function handle(NotifyTariffCode $event)
    {
        $tariffCodes = [];
        $ticket = $event->ticket;
        $user = $event->user;
        $agent = $event->agent;
        $ticketHtsList = $this->uhtsRepo->getTicketHtsList($user['id'], [['tuh.id', '!=', null], ['tuh.ticketId', $ticket['id']]]);
        foreach ($ticketHtsList as $ticketHts) {
            $tariffCodes[] = [
                'code' => $ticketHts['code'],
                'description' => trim($ticketHts['description']),
                'mergedDescription' => trim($ticketHts['mergedDescription']),
                'BasicDutyRateString' => $ticketHts['BasicDutyRateString'],
                'USTR301' => $ticketHts['USTR301']
            ];
        }

        $ticketNumber = TicketRepository::getTicketNumberFromId($ticket['id']);
        $isFreightosTicket = AffiliateRepository::isFreightosTicket($ticket['affiliateId']);
        if ($isFreightosTicket) {
            // Mail::to($user['email'])
            Mail::to('sandeepd.test@gmail.com')
                ->send(new FreightosTariffCode([
                    'tariffCodes' => $tariffCodes,
                    'ticketNumber' => $ticketNumber,
                    'firstname' => $user['firstname'],
                    'lastname' => $user['lastname'],
                    'agentFirstname' => $agent['firstname'],
                    'agentLastname' => $agent['lastname'],
                    'affiliateReferenceNumber' => $ticket['affiliateReferenceNumber'] ? '#' . $ticket['affiliateReferenceNumber'] : '',
                    // TODO: Generate signature url
                    'url' => '',
                ]));
        } else {
            // Mail::to($user['email'])
            Mail::to('sandeepd.test@gmail.com')
                ->send(new TariffCode([
                    'tariffCodes' => $tariffCodes,
                    'ticketNumber' => $ticketNumber,
                    'agentFirstname' => $agent['firstname'],
                    'agentLastname' => $agent['lastname'],
                    // TODO: Generate signature url
                    'url' => '',
                ]));
        }
    }
}
