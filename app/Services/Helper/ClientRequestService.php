<?php

namespace App\Services\Helper;

use App\Mail\Ticket\ClientRequests;
use App\Mail\Ticket\FreightosTicketClientRequests;
use App\Repositories\Eloquent\AffiliateRepository;
use App\Repositories\Eloquent\ClientRequestRepository;
use App\Repositories\Eloquent\DocUploadTypeRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\TicketRepository;
use App\Repositories\Eloquent\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ClientRequestService
{

    protected $clientReqRepo;
    protected $ticketRepo;
    protected $dutRepo;
    protected $notiRepo;
    protected $userRepo;

    public function __construct(
        ClientRequestRepository $clientReqRepo,
        TicketRepository $ticketRepo,
        DocUploadTypeRepository $dutRepo,
        NotificationRepository $notiRepo,
        UserRepository $userRepo
    ) {
        $this->clientReqRepo = $clientReqRepo;
        $this->ticketRepo = $ticketRepo;
        $this->dutRepo = $dutRepo;
        $this->notiRepo = $notiRepo;
        $this->userRepo = $userRepo;
    }

    public function list()
    {
        return $this->clientReqRepo->loadRepliesTab(auth()->user()->id);
    }

    public function markViewed($id)
    {
        return $this->clientReqRepo->markReadClientRequestNotification($id);
    }

    public function dailyMails($id)
    {
        return $this->clientReqRepo->getDailyClientRequestEmails($id);
    }

    public function bulkInsert($data)
    {
        $response = [];
        $affiliateConstants = config('constants.affiliate');
        $ticket = $this->ticketRepo->findOrFail($data['ticketId']);
        $ticketNumber = $this->ticketRepo->getTicketNumberFromId($ticket['id']);
        $customerId = $ticket['userid'];
        $user = auth()->user();
        $consumer = $this->userRepo->findOneByGuid($data['client_guid']);
        $isFreightosTicket = AffiliateRepository::isFreightosTicket($ticket['affiliateId']);
        foreach ($data['client_requests'] as $cr) {
            $doc = $this->dutRepo->findById($cr['documentTypeId']);
            if ($doc && $cr['description'] != '' && $doc['document_type'] == '') {
                $requestType = 'Information';
            } else {
                $requestType = 'Document';
            }
            $insertedCr = $this->clientReqRepo->create([
                'ticketId' => $ticket['id'],
                'userId' => $customerId,
                'requestType' => $requestType,
                'userRoleId' => $data['roleId'] ?? null,
                'description' => $cr['description'],
                'document' => $doc['document_type'] ?? null,
                'documentTypeId' => $cr['documentTypeId'],
                'sampleDocumentURL' => $cr['sampleDocumentURL']
            ]);
            $this->notiRepo->sendClientRequestNotification($insertedCr['id']);
            $response[] = $insertedCr;
        }

        if ($isFreightosTicket) {
            // Mail::to($consumer['email'])
            Mail::to('sandeepd.test@gmail.com')
                ->send(new FreightosTicketClientRequests([
                    'ticketNumber' => $ticketNumber,
                    'clientRequests' => $response,
                    'firstname' => $consumer['firstname'],
                    'lastname' => $consumer['lastname'],
                    'agentFirstname' => $user['firstname'],
                    'agentLastname' => $user['lastname'],
                    'isCustomerConsumer' => false,
                    'signUrl' => '',
                    'affiliateReferenceNumber' => $ticket['affiliateReferenceNumber'] ? "#" . $ticket['affiliateReferenceNumber'] : ''
                ]));
        } else {
            // Mail::to($consumer['email'])
            Mail::to('sandeepd.test@gmail.com')
                ->send(new ClientRequests([
                    'ticketNumber' => $ticketNumber,
                    'clientRequests' => $response,
                    'firstname' => $consumer['firstname'],
                    'lastname' => $consumer['lastname'],
                    'agentFirstname' => $user['firstname'],
                    'agentLastname' => $user['lastname'],
                    'isCustomerConsumer' => false,
                    'signUrl' => ''
                ]));
        }

        return $response;
    }

    public function markAsReceived($id)
    {
        $cr = $this->clientReqRepo->findOrFail($id);
        $this->clientReqRepo->update([['id', $id]], ['receivedOn' => (Carbon::now())->format('Y-m-d H:i:s')]);
        return $cr->refresh();
    }
}
