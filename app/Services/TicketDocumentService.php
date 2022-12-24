<?php

namespace App\Services;

use App\Events\Ticket\TicketDocumentUploaded;
use App\Helpers\Services\FreightosService;
use App\Mail\Ticket\ArrivalNotice;
use App\Models\TicketDocument;
use App\Repositories\Eloquent\AffiliateRepository;
use App\Repositories\Eloquent\DocUploadTypeRepository;
use App\Repositories\Eloquent\FreightMessageRepository;
use App\Repositories\Eloquent\TicketDocumentRepository;
use App\Repositories\Eloquent\TicketRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketDocumentService
{

    protected TicketDocumentRepository  $ticketDocRepo;
    protected TicketRepository          $ticketRepo;
    protected DocUploadTypeRepository   $docUploadTypeRepo;
    protected UserRepository            $userRepo;
    protected FreightMessageRepository  $freightMessageRepo;
    protected FreightosService          $fosService;

    public function __construct(
        TicketDocumentRepository $ticketDocRepo,
        TicketRepository $ticketRepo,
        DocUploadTypeRepository $docUploadTypeRepo,
        UserRepository $userRepo,
        FreightMessageRepository $freightMessageRepo,
        FreightosService $fosService
    ) {
        $this->ticketDocRepo = $ticketDocRepo;
        $this->ticketRepo = $ticketRepo;
        $this->docUploadTypeRepo = $docUploadTypeRepo;
        $this->userRepo = $userRepo;
        $this->freightMessageRepo = $freightMessageRepo;
        $this->fosService = $fosService;
    }

    public function delete($id)
    {
        $ticketDoc = $this->ticketDocRepo->findOrFail($id);
        $this->ticketRepo->findOrFail($ticketDoc['ticketId']);
        return $this->ticketDocRepo->softDelete($ticketDoc);
    }

    public function updateDocUploadType($id, $data)
    {
        $ticketDoc = $this->ticketDocRepo->findOrFail($id);
        $docUploadType = $this->docUploadTypeRepo->findOrFail($data['documentUploadTypeId']);
        $update = [
            'documentUploadTypeId' => $data['documentUploadTypeId'],
            'description' => $docUploadType['document_type']
        ];

        $this->ticketDocRepo->update([['id', $ticketDoc['id']]], $update);
        return $ticketDoc->refresh();
    }

    public function create($data)
    {
        $ticketConstants = config('constants.ticket');
        $ticket = $this->ticketRepo->findOrFail($data['ticketId']);
        $docUploadType = $this->docUploadTypeRepo->findById($data['documentUploadTypeId']);

        if ($ticket['type'] != $ticketConstants['type']['CAR']) {
            $user = $this->userRepo->findById($ticket['userid']);
            $isFreightosTicket = AffiliateRepository::isFreightosTicket($ticket['affiliateId']);

            $filePath = Storage::putFile(TicketDocument::DOCPATH, $data['document']);

            $ticketDocument = $this->ticketDocRepo->create([
                'description' => @$docUploadType['document_type'],
                'documentUploadTypeId' => $data['documentUploadTypeId'],
                'file_description' => $data['file_description'],
                'ticketId' => $ticket['id'],
                'userId' => $ticket['userid'],
                'file' => $filePath,
            ]);

            if (!$isFreightosTicket && @$docUploadType['document_type'] == 'Arrival Notice') {
                Mail::to($user['email'])->send(new ArrivalNotice);
                $message = 'Please note in order for your cargo to be released from it\'s warehouse, regardless of the customs status, all Forwarder & Arrival notice fees* must be paid in full. If you would like Clearit to handle the payments on your behalf, please notify your agent.';
                $this->freightMessageRepo->createChatMessage(Str::upper(Str::uuid()), $ticket['guid'], $message, 0, 1);
            }

            if ($isFreightosTicket && isset($this->fosService::$mappedDocumentTypes[@$docUploadType['document_type']])) {
                $udt = $this->fosService::$mappedDocumentTypes[@$docUploadType['document_type']];
                $this->fosService->uploadShipmentAttachment($ticket['affiliateReferenceNumber'], $udt, $ticketDocument['guid'], $data['document']);
            }

            if ($isFreightosTicket && @$docUploadType['document_type'] == "ISF FILE") {
                $update = [];
                $update['status']             = "Freightos - ISF";
                $update['substatus']          = "Ready to File";
                $update['agentStatusTypeId']  = "256";
                $this->ticketRepo->update([['id', $ticket['id']]], $update);
                $ticket->refresh();
                event(new TicketDocumentUploaded($ticket, 'ISF'));
            }

            return $ticketDocument;
        }
        return null;
    }
}
