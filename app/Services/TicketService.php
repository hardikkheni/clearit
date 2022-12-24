<?php

namespace App\Services;

use App\Events\Ticket\NotifyTariffCode;
use App\Helpers\Services\RightSignatureService;
use App\Mail\Ticket\EtaUpdated;
use App\Mail\Ticket\NewAdditionalCard;
use App\Mail\Ticket\FreightosTicketInvoiceCreated;
use App\Mail\Ticket\IsfFiled;
use App\Mail\Ticket\NewAdditionalNoCard;
use App\Mail\Ticket\NewCardPayment;
use App\Mail\Ticket\NewCardPaymentDecline;
use App\Mail\Ticket\NewTicketCharge;
use App\Mail\Ticket\NewTicketChargeNoPayment;
use App\Mail\Ticket\NewTicketNoCharge;
use App\Mail\Ticket\NewTicketPaid;
use App\Mail\Ticket\RefundRequest;
use App\Mail\Ticket\TicketNotify;
use App\Models\Ticket;
use App\Repositories\Eloquent\{
    AffiliateRepository,
    AgentMessageRepository,
    AgentStatusTypeRepository,
    ClientRequestRepository,
    ContainerRepository,
    DocUploadTypeRepository,
    FFContactRepository,
    FreightForwarderRepository,
    FreightMessageRepository,
    FreightosChargeRepository,
    FreightosInvoiceItemRepository,
    InvoiceRepository,
    MessageRepository,
    NoteRepository,
    NotificationRepository,
    PaymentRepository,
    PgaRepository,
    PgaRequestRepository,
    ReminderRepository,
    TicketDocumentRepository,
    TicketInvoiceRepository,
    TicketItemRepository,
    TicketRepository,
    TicketStatusRepository,
    TicketUserHtsRepository,
    ToDoTicketItemCompletedRepository,
    ToDoTicketItemRepository,
    UserDocumentRepository,
    UserHtsRepository,
    UserRepository,
    UserRoleRepository,
    UserSoldToRepository,
    UserVendorRepository,
};
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class TicketService
{

    protected $srService;
    protected $ticketRepo;
    protected $ticketStatusRepo;
    protected $toDoTicketItemRepo;
    protected $userRoleRepo;
    protected $notificationRepo;
    protected $messageRepo;
    protected $fmsgRepo;
    protected $userRepo;
    protected $noteRepo;
    protected $tDocRepo;
    protected $dutRepo;
    protected $crRepo;
    protected $uhtsRepo;
    protected $tuhtsRepo;
    protected $containerRepo;
    protected $reminderRepo;
    protected $affiliateRepo;
    protected $fiiRepo;
    protected $fcRepo;
    protected $paymentRepo;
    protected $uDocRepo;
    protected $tItemRepo;
    protected $invoiceRepo;
    protected $tInvoiceRepo;
    protected $uVendorRepo;
    protected $ffRepo;
    protected $ffcRepo;
    protected $astRepo;
    protected $tdticRepo;
    protected $pgaRepo;
    protected $pgaReqRepo;
    protected $amRepo;
    protected $soldToRepo;

    public function __construct(
        RightSignatureService $srService,
        TicketRepository $ticketRepo,
        TicketStatusRepository $ticketStatusRepo,
        ToDoTicketItemRepository $toDoTicketItemRepo,
        UserRoleRepository $userRoleRepo,
        NotificationRepository $notificationRepo,
        MessageRepository $messageRepo,
        FreightMessageRepository $fmsgRepo,
        UserRepository $userRepo,
        NoteRepository $noteRepo,
        TicketDocumentRepository $tDocRepo,
        DocUploadTypeRepository $dutRepo,
        ClientRequestRepository $crRepo,
        UserHtsRepository $uhtsRepo,
        TicketUserHtsRepository $tuhtsRepo,
        ContainerRepository $containerRepo,
        ReminderRepository $reminderRepo,
        AffiliateRepository $affiliateRepo,
        FreightosInvoiceItemRepository $fiiRepo,
        FreightosChargeRepository $fcRepo,
        PaymentRepository $paymentRepo,
        UserDocumentRepository $uDocRepo,
        TicketItemRepository $tItemRepo,
        InvoiceRepository $invoiceRepo,
        TicketInvoiceRepository $tInvoiceRepo,
        UserVendorRepository $uVendorRepo,
        FreightForwarderRepository $ffRepo,
        FFContactRepository $ffcRepo,
        AgentStatusTypeRepository $astRepo,
        ToDoTicketItemCompletedRepository $tdticRepo,
        PgaRepository $pgaRepo,
        PgaRequestRepository $pgaReqRepo,
        AgentMessageRepository $amRepo,
        UserSoldToRepository $soldToRepo
    ) {
        $this->srService = $srService;
        $this->ticketRepo = $ticketRepo;
        $this->ticketStatusRepo = $ticketStatusRepo;
        $this->toDoTicketItemRepo = $toDoTicketItemRepo;
        $this->userRoleRepo = $userRoleRepo;
        $this->notificationRepo = $notificationRepo;
        $this->messageRepo = $messageRepo;
        $this->fmsgRepo = $fmsgRepo;
        $this->userRepo = $userRepo;
        $this->noteRepo = $noteRepo;
        $this->tDocRepo = $tDocRepo;
        $this->dutRepo = $dutRepo;
        $this->crRepo = $crRepo;
        $this->uhtsRepo = $uhtsRepo;
        $this->tuhtsRepo = $tuhtsRepo;
        $this->containerRepo = $containerRepo;
        $this->reminderRepo = $reminderRepo;
        $this->affiliateRepo = $affiliateRepo;
        $this->fiiRepo = $fiiRepo;
        $this->fcRepo = $fcRepo;
        $this->paymentRepo = $paymentRepo;
        $this->uDocRepo = $uDocRepo;
        $this->tItemRepo = $tItemRepo;
        $this->invoiceRepo = $invoiceRepo;
        $this->tInvoiceRepo = $tInvoiceRepo;
        $this->uVendorRepo = $uVendorRepo;
        $this->ffRepo = $ffRepo;
        $this->ffcRepo = $ffcRepo;
        $this->astRepo = $astRepo;
        $this->tdticRepo = $tdticRepo;
        $this->pgaRepo = $pgaRepo;
        $this->pgaReqRepo = $pgaReqRepo;
        $this->amRepo = $amRepo;
        $this->soldToRepo = $soldToRepo;
    }

    public function getTicketStatusDependencies($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            $type = $ticketConstants['type']['CLEARANCE'];
        }
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        if (!in_array($transport, $ticketConstants['transportModes'])) {
            $transport = $ticketConstants['transport']['TRUCK'];
        }
        $ticketStatusId = $data['status'] ?? null;
        $ticketStatus = null;
        if ($ticketStatusId) {
            $ticketStatus = $this->ticketStatusRepo->findById($ticketStatusId);
        }
        if ($ticketStatus && ($ticketStatus['ticketType'] != $type || $type == $ticketConstants['type']['CLEARANCE'] && $ticketStatus['ticketTransport'] != $transport)) {
            $ticketStatus = null;
        }
        if (!$ticketStatus) {
            $ticketStatusId = null;
        }
        $sorts = [['displayOrder', 'ASC']];
        $where = [['ticketType', '=', $type]];
        if ($type == $ticketConstants['type']['CLEARANCE']) {
            $where[] = ['ticketTransport', '=', $transport];
        }
        $ticketStatusList = $this->ticketStatusRepo->find($where, $sorts);
        if (!$ticketStatusId && count($ticketStatusList) > 0) {
            $ticketStatusId = $ticketStatusList->first()->id;
        }
        $toDoTicketItemList = $this->toDoTicketItemRepo->find($where, $sorts);
        $toDoItemDependencyIds = $this->ticketStatusRepo->toDoTicketItemListByStatusId($ticketStatusId)->pluck('id');
        return [
            'ticketStatusList' => $ticketStatusList,
            'toDoTicketItemList' => $toDoTicketItemList,
            'toDoItemDependencyIds' => $toDoItemDependencyIds,
            'type' => $type,
            'transport' => $transport,
            'status' => $ticketStatusId,
        ];
    }

    public function putTicketStatusDependencies($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        $ticketStatusId = $data['status'] ?? null;
        $toDoItemDependencyIds = $data['toDoItemDependencyIds'];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            throw ValidationException::withMessages([
                'type' => ['Invalid ticket type']
            ]);
        }
        if ($type == $ticketConstants['type']['CLEARANCE'] && !in_array($transport, $ticketConstants['transportModes'])) {
            throw ValidationException::withMessages([
                'transport' => ['Invalid ticket transport']
            ]);
        }
        $ticketStatus = $this->ticketStatusRepo->findOrFail($ticketStatusId);
        if ($ticketStatus && ($ticketStatus['ticketType'] != $type || $type == $ticketConstants['type']['CLEARANCE'] && $ticketStatus['ticketTransport'] != $transport)) {
            throw ValidationException::withMessages([
                'status' => ['Ticket Status doesn\'t match selected Ticket Type or Transport Mode']
            ]);
        }
        foreach ($toDoItemDependencyIds as $toDoTicketItemId) {
            $toDoTicketItem = $this->toDoTicketItemRepo->findById($toDoTicketItemId);
            if ($toDoTicketItem && ($toDoTicketItem['ticketType'] != $type || $type == $ticketConstants['type']['CLEARANCE'] && $toDoTicketItem['ticketTransport'] != $transport)) {
                throw ValidationException::withMessages([
                    'toDoItemDependencyIds' => ['To Do Item doesn\'t match selected Ticket Type or Transport Mode']
                ]);
            }
        }

        return $this->ticketStatusRepo->syncTodoTicketItemsByStatusId($ticketStatusId, $toDoItemDependencyIds, true);
    }

    public function getTodoTicketItemList($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        $role = @$data['role'];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            $type = $ticketConstants['type']['CLEARANCE'];
        }
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        if (!in_array($transport, $ticketConstants['transportModes'])) {
            $transport = $ticketConstants['transport']['TRUCK'];
        }
        $where = [['ticketType', '=', $type]];
        $sorts = [['displayOrder', 'ASC']];
        if ($role) {
            $where[] = ['userRoleId', '=', $role];
        } else {
            $where[] = ['userRoleId', null];
        }
        if ($type == $ticketConstants['type']['CLEARANCE']) {
            $where[] = ['ticketTransport', '=', $transport];
        }
        return $this->toDoTicketItemRepo->find($where, $sorts);
    }

    public function upsertTodoTicketItem($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        $role = $data['role'] ?? null;
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            throw ValidationException::withMessages([
                'type' => ['Invalid ticket type']
            ]);
        }
        if ($type == $ticketConstants['type']['CLEARANCE'] && !in_array($transport, $ticketConstants['transportModes'])) {
            throw ValidationException::withMessages([
                'transport' => ['Invalid ticket transport']
            ]);
        }
        if (!$role) {
            throw ValidationException::withMessages([
                'status' => ['Invalid ticket user role']
            ]);
        }
        if (@$data['displayOrder']) {
            $this->toDoTicketItemRepo->shiftFromDisplayOrder($data['displayOrder']);
        }
        return $this->toDoTicketItemRepo->upsert(['id' => @$data['id']], [
            'itemName' => $data['itemName'],
            'ticketType' => $type,
            'ticketTransport' => $type == $ticketConstants['type']['CLEARANCE'] ? $transport : null,
            'userRoleId' => $role,
            'displayOrder' => @$data['displayOrder']
        ]);
    }

    public function deleteTodoTicketItem($id)
    {
        return $this->toDoTicketItemRepo->delete($id);
    }

    public function reOrderTodoTicketItemList($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        $role = $data['role'] ?? null;
        $list = $data['list'] ?? [];
        $newList = [];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            throw ValidationException::withMessages([
                'type' => ['Invalid ticket type']
            ]);
        }
        if ($type == $ticketConstants['type']['CLEARANCE'] && !in_array($transport, $ticketConstants['transportModes'])) {
            throw ValidationException::withMessages([
                'transport' => ['Invalid ticket transport']
            ]);
        }
        if (!$role) {
            throw ValidationException::withMessages([
                'status' => ['Invalid ticket user role']
            ]);
        }
        foreach ($list as $key => $todo) {
            $newList[] = $this->toDoTicketItemRepo->upsert(['id' => @$todo['id']], [
                'itemName' => $todo['itemName'],
                'ticketType' => $type,
                'ticketTransport' => $type == $ticketConstants['type']['CLEARANCE'] ? $transport : null,
                'userRoleId' => $role,
                'displayOrder' => $key + 1
            ]);
        }
        return $newList;
    }

    public function getTicketMaster($guid, $notificationId)
    {
        $userConstants = config('constants.user');
        $ticketConstants = config('constants.ticket');
        $includeDeleted = true;
        $ticket = $this->ticketRepo->findOneByGuid($guid, $includeDeleted);
        $agentId = auth()->user()->id;
        if (!$ticket) throw new \Exception("Ticket: $guid doesn't exist!.");

        $userId = $ticket['userid'];
        $this->notificationRepo->markReadAgentNotification($notificationId);
        $this->notificationRepo->markReadTicketUploadAgentNotifications($ticket->id);
        $this->ticketRepo->resetNew($ticket->id);
        $agentMessages = $this->amRepo->findByTicketId($ticket->id);
        $this->ticketRepo->update(['id' => $ticket->id], [
            'isNewTicket' => false,
            'isnewnote' => false,
            'isNew' => false,
        ]);
        $this->messageRepo->markViewed($ticket->id);
        $client = $this->userRepo->findById($userId);
        $payments_count = $this->paymentRepo->countByUserId($userId);
        $ticket['user_firstname'] = $client['firstname'];
        $ticket['user_lastname'] = $client['lastname'];
        $ticket['user_guid'] = $client['guid'];
        $ticket['iscc'] = $payments_count;
        $ticket['ispoa'] = $client['issigned'];
        $ticket['isverified'] = $client['isverified'];
        $ticket['isreference'] = $client['isreference'];
        $ticket['iscertificate'] = $client['iscertificate'];
        $ticket['ispga'] = $this->uDocRepo->findAndOrSort([['userid', $userId,], ['documenttype', 2]])->count();
        $agents = $this->userRepo->getAgentList(['displayInternally' => true, 'isActive' => true], [['firstname', 'ASC']]);
        $messages = $this->messageRepo->getMessageListByTicketId($ticket->id);
        $freight_messages = $this->fmsgRepo->getMessageListByTicketId($ticket->id);
        $notes = $this->noteRepo->findByTicketId($ticket->id);
        $notes->load('user');
        $documents = $this->tDocRepo->getTicketDocumentListByTicketId($ticket->id);
        $items = $this->tItemRepo->findByTicketId($ticket->id);
        $count_charge = 0;
        if ($items) {
            foreach ($items as $item) {
                if ($item['isneedcharge']) $count_charge++;
            }
        }
        $invoices = $this->tInvoiceRepo->findByTicketId($ticket->id);
        foreach ($invoices as $key => $invoice) {
            $vendorId = $invoice['vendorId'];
            $vendor = $this->uVendorRepo->findById($vendorId);
            $invoices[$key]['vendor'] = $vendor;
            $invoiceId = $invoice['id'];
            $ticketItems = $this->tItemRepo->find([['ticketinvoiceid', $invoiceId]]);
            $invoices[$key]['items'] = $ticketItems;
        }
        $user = $this->userRepo->findById($ticket['userid']);
        $uploadedPersonalDocs = [];
        $userDocList = $this->uDocRepo->findByUserId($ticket['userid']);
        if ($userDocList) {
            foreach ($userDocList as $userDoc) {
                if (in_array($userDoc['documentType'], $userConstants['personal_id_user_document_types'])) {
                    $uploadedPersonalDocs[] = $userDoc;
                }
            }
        }
        $ticketNotifications = $this->notificationRepo->getTicketNotifications($ticket->id);
        $ticketAffiliate = $this->affiliateRepo->findById($ticket->affiliateId);
        $isFreightosTicket = $this->affiliateRepo->isFreightosTicket($ticketAffiliate);

        switch ($ticket->type) {
            case $ticketConstants['type']['CLEARANCE']:
                $documentUploadTypes = $this->dutRepo->find(['shipping_method' => $ticket->transport], [['display_order', 'ASC']]);
                $missingDocuments = $this->dutRepo->getMissingDocumentUploadTypeListByTicket($ticket);
                $commodities = $this->uhtsRepo->getTicketHtsList($userId);
                $htsCodes = $this->uhtsRepo->getAllHtsListByUserId($userId);
                $isfConsolidatorList = $this->ffRepo->find([], [['createdOn', 'ASC']]);
                $containerTrakingDetail = $this->containerRepo->getContainerTraking($ticket['containerNumber']);
                $canRequireDelivery = in_array($ticket['transport'], $ticketConstants['transportModesRequiresDelivery']);
                $affiliates = $this->affiliateRepo->find(['isActive' => true], [['companyname', 'ASC']]);
                $soldTo = $this->soldToRepo->findByTicket($ticket);
                break;
            case $ticketConstants['type']['FREIGHT']:
            case $ticketConstants['type']['CAR']:
                $items = @unserialize($ticket['freightItems']);
                break;
        }

        $affiliate = $this->affiliateRepo->findById($client['affiliateId']);
        $isFreightosUser = ($affiliate && $affiliate->affiliateCode == config('constants.affiliate.FREIGHTOSCODE'));

        if (!$messages->count()) {
            $this->messageRepo->presetMessage($ticket['id'], $isFreightosUser);
            $messages = $this->messageRepo->getMessageListByTicketId($ticket);
        }

        $sorts = [['displayOrder', 'ASC']];
        $where = [['ticketType', $ticket['type']]];
        $agentStatusTypes = $this->astRepo->find($where, $sorts);
        if ($ticket['type'] == $ticketConstants['type']['CLEARANCE']) {
            $where[] = ['ticketTransport', $ticket['transport']];
        }
        $ticketStatusList = $this->ticketStatusRepo->find($where, $sorts);
        $groupedTicketStatuses = $this->ticketStatusRepo->groupTicketStatusesByStatusName($ticketStatusList);
        $toDoTicketItems = $this->toDoTicketItemRepo->getUncheckedToDoTicketItemList($where, $sorts, $ticket['id']);
        $checkedToDoTicketItems = $this->tdticRepo->getCheckedToDoTicketItemsByTicket($where, [['displayOrder', 'ASC']], $ticket['id']);
        $pgaDocs = $this->pgaRepo->find();
        $freightInvoiceDocuments = $this->fiiRepo->find();
        $freightosChargesDocs = $this->fiiRepo->getFreightInvoiceItemList($ticket['id']);
        $pgaRequests = $this->pgaReqRepo->getPgaRequestListByTicketId($ticket['id']);
        $reminderItems = $this->reminderRepo->getPendingReminderList($ticket['id'], [], [['dueOn', 'ASC']]);
        $checkedReminderItems = $this->reminderRepo->getCheckedReminderList($ticket['id'], [], [['dueOn', 'ASC']]);
        $showRemindersTab = false;
        foreach ($reminderItems as $reminderItem) {
            if (!empty($reminderItem['is_past_due']) || !empty($reminderItem['is_today_due'])) {
                $showRemindersTab = true;
                break;
            }
        }
        $cargoReadyDate = '';
        if ($ticket['cargoreadydate']) {
            try {
                $cargoReadyDate = (new \DateTime($ticket['cargoreadydate']))->format('Y-m-d');
            } catch (\Exception $e) {
            }
        }
        $statusHistory = $this->ticketRepo->getTicketSBmessageHistoryByTicket($ticket);
        $clientRequests = $this->crRepo->find([['ticketId', $ticket['id']]]);

        $agent =  $this->userRepo->findById($ticket['agentid']);
        $processingAgent =  $this->userRepo->findById($ticket['processingAgentId']);
        return [
            'guid' => $guid,
            'client' => $client,
            'agents' => $agents,
            'agent' => $agent,
            'processingAgent' => $processingAgent,
            'ticket_user' => $user,
            'ticket' => $ticket,
            'documents' => $documents,
            'items' => $items,
            'invoices' => $invoices,
            'messages' => $messages,
            'freight_messages' => $freight_messages,
            'agentMessages' => $agentMessages,
            'notes' => $notes,
            'count_charge' => $count_charge,
            'agentStatusTypes' => $agentStatusTypes,
            'ticketStatusList' => $ticketStatusList,
            'groupedTicketStatuses' => $groupedTicketStatuses,
            'uploadedPersonalDocs' => $uploadedPersonalDocs,
            'ticketNotifications' => $ticketNotifications,
            'toDoTicketItems' => $toDoTicketItems,
            'checkedToDoTicketItems' => $checkedToDoTicketItems,
            'reminderItems' => $reminderItems,
            'clientRequests' => $clientRequests,
            'checkedReminderItems' => $checkedReminderItems,
            'showRemindersTab' => $showRemindersTab,
            'affiliate' => $affiliate,
            'affiliates' => $affiliates ?? [],
            'soldTo' => $soldTo ?? (object) [],
            'isfConsolidatorList' => $isfConsolidatorList ?? [],
            'pgaDocuments' => $pgaDocs,
            'freightosChargesDocs' => $freightosChargesDocs,
            'freightInvoiceDocuments' => $freightInvoiceDocuments,
            'pgaRequests' => $pgaRequests,
            'cargoReadyDate' => $cargoReadyDate,
            'ticketAffiliate' => $ticketAffiliate,
            'isFreightosTicket' => $isFreightosTicket,
            'statusHistory' => $statusHistory,
            'missingDocuments' => $missingDocuments ?? [],
            'documentUploadTypes' => $documentUploadTypes ?? [],
            'commodities' => $commodities ?? [],
            'htsCodes' => $htsCodes ?? [],
            'containerTrakingDetail' => $containerTrakingDetail ?? (object) []
        ];
    }

    public function getTicketRole($role, $guid, $notificationId)
    {
        $userRole = $this->userRoleRepo->findOne([['internalKey', $role]]);
        // Allow user to view deleted ticket.
        $includeDeleted = true;
        $ticket = $this->ticketRepo->findOneByGuid($guid, $includeDeleted);
        $agentId = auth()->user()->id;
        if (!$ticket) throw new \Exception("Ticket: $guid doesn't exist!.");

        $userId = $ticket['userid'];
        $this->notificationRepo->markReadAgentNotification($notificationId);
        $this->notificationRepo->markReadTicketUploadAgentNotifications($ticket->id);
        $this->ticketRepo->resetNew($ticket->id);
        $this->ticketRepo->update(['id' => $ticket->id], [
            'isNewTicket' => false,
            'isnewnote' => false,
            'isNew' => false,
        ]);
        $this->messageRepo->markViewed($ticket->id);
        $client = $this->userRepo->findById($userId);
        $agents = $this->userRepo->getAgentList(['displayInternally' => true, 'isActive' => true], [['firstname', 'ASC']]);
        $messages = $this->messageRepo->getMessageListByTicketId($ticket->id);
        $freight_messages = $this->fmsgRepo->getMessageListByTicketId($ticket->id);
        $notes = $this->noteRepo->findByTicketId($ticket->id);
        $notes->load('user');
        $getFullTicket = $this->ticketRepo->getFullTicket($ticket->id, $agentId, $userRole->id);
        $ticketDocData = $this->tDocRepo->getTicketAllDocumentByTicketId($getFullTicket['ticketId'], 'Clearit Final Invoice');
        $documents = $this->tDocRepo->getTicketDocumentListByTicketId($ticket->id);
        $ticketNotifications = $this->notificationRepo->getTicketNotifications($ticket->id);
        $isFreightosTicket = $getFullTicket['affiliateCode'] == config('constants.affiliate.FREIGHTOSCODE');
        $documentUploadTypesList = $this->dutRepo->find(['shipping_method' => $ticket->transport], [['display_order', 'ASC']]);
        $clientRequestItems = $this->crRepo->getClientRequestItems($userRole->id, $ticket->transport);
        $htsCodes = $this->uhtsRepo->getAllHtsListByUserId($userId);
        $canRequireDelivery = in_array($getFullTicket['transportMode'], config('constants.ticket.transportModesRequiresDelivery'));
        $containerTrakingDetail = $this->containerRepo->getContainerTraking($getFullTicket['containerNumber']);
        $checkedReminderItems = $this->reminderRepo->getCheckedReminderList($ticket->id, [], [['dueOn', 'ASC']]);

        $showRemindersTab = false;
        if (!empty($getFullTicket['reminder_items_data']) && isset($getFullTicket['reminder_items_data'])) {
            foreach ($getFullTicket['reminder_items_data'] as $reminderItem) {
                if (!empty($reminderItem->is_past_due) || !empty($reminderItem->is_today_due)) {
                    $showRemindersTab = true;
                    break;
                }
            }
        }

        $rolesList = $this->userRoleRepo->active()->reduce(function ($accu, $role) {
            $accu[$role->internalKey] = $role;
            return $accu;
        }, []);

        $getLatestDoc = (object) [];
        $documentUploadTypeId = $this->dutRepo->getDocumentUploadTypeListByConstant(config('constants.document_upload_type.type.ISF_CERTIFICATE'), 2);
        if ($documentUploadTypeId > 0) {
            $getLatestDoc = $this->dutRepo->getLatestTicketDocument($getFullTicket['ticketId'], $documentUploadTypeId);
        }

        $missingDocuments = $this->dutRepo->getMissingDocumentUploadTypeListByTicket($ticket);

        $affiliate = $this->affiliateRepo->findById($getFullTicket['userAffiliateId']);
        $isFreightosUser = ($affiliate && $affiliate->affiliateCode == config('constants.affiliate.FREIGHTOSCODE'));

        if (!$messages->count()) {
            $this->messageRepo->presetMessage($ticket['id'], $isFreightosUser);
            $messages = $this->messageRepo->getMessageListByTicketId($ticket);
        }

        $freightInvoiceDocuments = $this->fiiRepo->find();
        $statusHistory = $this->ticketRepo->getTicketSBmessageHistoryByTicket($ticket);

        return [
            'documentUploadTypeId' => $documentUploadTypeId,
            'htsCodes' => $htsCodes,
            'missingDocuments' => $missingDocuments,
            'documentUploadTypesList' => $documentUploadTypesList,
            'clientRequestItems' => $clientRequestItems,
            'role' => $userRole,
            'rolesList' => $rolesList,
            'roleId' => $userRole['id'],
            'getFullTicket' => $getFullTicket,
            'checkedReminderItems' => $checkedReminderItems,
            'showRemindersTab' => $showRemindersTab,
            'getLatestDoc' => $getLatestDoc,
            'ticketNotifications' => $ticketNotifications,
            'canRequireDelivery' => $canRequireDelivery,
            'containerTrakingDetail' => $containerTrakingDetail,
            'ticketDocData' => $ticketDocData,
            'guid' => $guid,
            'user' => $client,
            'agents' => $agents,
            'ticket' => $ticket,
            'documents' => $documents,
            'messages' => $messages,
            'freight_messages' => $freight_messages,
            'notes' => $notes,
            'freightInvoiceDocuments' => $freightInvoiceDocuments,
            'isFreightosTicket' => $isFreightosTicket,
            'statusHistory' => $statusHistory,
        ];
    }

    public function freightosBillingData($data)
    {
        $isBilled = $data['isBilled'] ?? 0;
        $from = $data['from'] ?? null;
        $to = $data['to'] ?? null;
        return $this->ticketRepo->getFreightosBilling($isBilled, $from, $to);
    }

    public function getFreightInvoiceItemList($id)
    {
        return $this->fiiRepo->getFreightInvoiceItemList($id);
    }

    public function deleteFreightInvoiceItem($invoiceItemId)
    {
        return $this->fiiRepo->deleteFreightInvoiceItem($invoiceItemId);
    }

    public function getFcDatetime($id)
    {
        return $this->fcRepo->getFcDateTimeByTicketId($id);
    }

    public function getFcInvoiceDatetime($id)
    {
        return $this->ticketRepo->getFcInvoiceDatetime($id);
    }

    public function getTicketStatusList($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            $type = $ticketConstants['type']['CLEARANCE'];
        }
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        if (!in_array($transport, $ticketConstants['transportModes'])) {
            $transport = $ticketConstants['transport']['TRUCK'];
        }
        $where = [['ticketType', '=', $type]];
        $sorts = [['displayOrder', 'ASC']];
        if ($type == $ticketConstants['type']['CLEARANCE']) {
            $where[] = ['ticketTransport', '=', $transport];
        }
        return $this->ticketStatusRepo->find($where, $sorts);
    }

    public function deleteTicketStatus($id)
    {
        return $this->ticketStatusRepo->delete($id);
    }

    public function upsertTicketStatus($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            throw ValidationException::withMessages([
                'type' => ['Invalid ticket type']
            ]);
        }
        if ($type == $ticketConstants['type']['CLEARANCE'] && !in_array($transport, $ticketConstants['transportModes'])) {
            throw ValidationException::withMessages([
                'transport' => ['Invalid ticket transport']
            ]);
        }
        if (@$data['displayOrder']) {
            $this->toDoTicketItemRepo->shiftFromDisplayOrder($data['displayOrder']);
        }
        return $this->ticketStatusRepo->upsert(['id' => @$data['id']], [
            'statusName' => $data['statusName'],
            'ticketType' => $type,
            'ticketTransport' => $type == $ticketConstants['type']['CLEARANCE'] ? $transport : null,
            'hexColor' => $data['hexColor'],
            'textHexColor' => $data['textHexColor'],
            'substatus' => $data['substatus'],
            'displayOrder' => @$data['displayOrder']
        ]);
    }

    public function reOrderTicketStatusList($data)
    {
        $ticketConstants = config('constants.ticket');
        $type = $data['type'] ?? $ticketConstants['type']['CLEARANCE'];
        $transport = $data['transport'] ?? $ticketConstants['transport']['TRUCK'];
        $list = $data['list'] ?? [];
        $newList = [];
        if (!in_array($type, $ticketConstants['ticketTypes'])) {
            throw ValidationException::withMessages([
                'type' => ['Invalid ticket type']
            ]);
        }
        if ($type == $ticketConstants['type']['CLEARANCE'] && !in_array($transport, $ticketConstants['transportModes'])) {
            throw ValidationException::withMessages([
                'transport' => ['Invalid ticket transport']
            ]);
        }
        foreach ($list as $key => $status) {
            $newList[] = $this->ticketStatusRepo->upsert(['id' => @$status['id']], [
                'statusName' => $status['statusName'],
                'ticketType' => $type,
                'ticketTransport' => $type == $ticketConstants['type']['CLEARANCE'] ? $transport : null,
                'displayOrder' => $key + 1
            ]);
        }
        return $newList;
    }

    public function getArrivalNoticeTickets($isKeyed, $page = 1, $limit = 30)
    {
        return $this->ticketRepo->getArrivalNoticeTickets($isKeyed, ($page - 1) * $limit, $limit);
    }

    public function getPrekeyBillingTickets($page = 1, $limit = 30)
    {
        return $this->ticketRepo->getPrekeyBillingTickets(($page - 1) * $limit, $limit);
    }

    public function getReleaseTeamTickets($page = 1, $limit = 30)
    {
        return $this->ticketRepo->getReleaseTeamTickets(($page - 1) * $limit, $limit);
    }

    public function getIsfTickets($page = 1, $limit = 30)
    {
        return $this->ticketRepo->getIsfTickets(($page - 1) * $limit, $limit);
    }

    public function getISFNotFiled($isVerified, $page = 1, $limit = 30)
    {
        return $this->ticketRepo->getISFNotFiled($isVerified, ($page - 1) * $limit, $limit);
    }

    public function updateRequireBrokerReview($id, $requires_broker_review)
    {
        $ticket = $this->ticketRepo->findById($id);
        if (!$ticket) throw new ModelNotFoundException("ticket: {$ticket->id} not found!");
        if ($ticket['requires_broker_review'] != $requires_broker_review) {
            $this->ticketRepo->update([['id', $ticket['id']]], ['requires_broker_review' => $requires_broker_review]);
        } else {
            $requires_broker_review = !!$ticket['requires_broker_review'];
        }
        $message = $requires_broker_review ? 'This ticket has been submitted for broker review.' : 'This ticket has been removed from broker review.';
        return ['ticketId' => $ticket->id, 'requires_broker_review' => $requires_broker_review, 'alert' => ['variant' => 'success', 'message' => $message]];
    }

    public function patch($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $this->ticketRepo->update([['id', $ticket['id']]], $data);
        return $ticket->refresh();
    }

    public function delete($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $ticket = $this->ticketRepo->delete($ticket, $data['reason']);
        if ($ticket) {
            $this->ticketRepo->deleteTicketUnpaidAmount($ticket);
            $user = auth()->user();
            $username = $user['firstname'] . " " . substr($user['lastname'], 0, 1);
            $this->notificationRepo->sendNotification([
                'userId' => $ticket['userid'],
                'ticketId' => $ticket['id'],
                'description' => sprintf('Ticket #%s was deleted by %s', $this->ticketRepo->getTicketNumberFromId($ticket['id']), $username),
                'type' => 'ticket_delete',
            ], 0, 1);
        }
    }

    public function updateTicketStatus($id, $statusId)
    {
        $ticketStatus = $this->ticketStatusRepo->findOrFail($statusId);
        $status = $ticketStatus['statusName'];
        $substatus = $ticketStatus['substatus'];
        $ticket = $this->ticketRepo->findOrFail($id);
        $ticketConstants = config('constants.ticket');
        $update = ['substatus' => $substatus, 'status' => $status];
        $userId = auth()->user()->id;

        // *** some general ticket updates
        if (
            $substatus &&
            ($ticket['status'] != $ticketConstants['status']['TO_QUOTE'] || $ticket['substatus'] != $substatus) &&
            $status == $ticketConstants['status']['TO_QUOTE']
        ) {
            $this->messageRepo->newMessage([
                'guid' => Str::upper(Str::uuid()),
                'ticketId' => $ticket['id'],
                'message' => "System message: $substatus",
                'userId' => $userId,
                'isMaster' => true
            ]);
        }

        // Add message about ISF
        if ($ticket['status'] != $ticketConstants['status']['ISF'] && $status == $ticketConstants['status']['ISF']) {
            $this->messageRepo->newMessage([
                'guid' => Str::upper(Str::uuid()),
                'ticketId' => $ticket['id'],
                'message' => "ISF filed - Please forward Arrival Notice once received",
                'userId' => $userId,
                'isMaster' => true
            ]);
            $update['isNew1'] = $userId;
        }
        $this->ticketRepo->update([['id', $id]], $update);
        return $ticket->refresh();
    }

    public function updateAgent($id, $agentid)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $agent = $this->userRepo->findOneByGuid($agentid);
        if ($agent && $agent['isAgent']) {
            $this->ticketRepo->update([['id', $ticket['id']]], ['agentid' => $agent['id']]);
            $this->notificationRepo->sendNotification([
                'userId' => auth()->user()->id,
                'ticketId' => $ticket['id'],
                'description' => 'Ticket assigned to ' . $agent['firstname'] . ' ' . $agent['lastname'],
                'type' => 'assign',
            ]);
            return $ticket->refresh();
        }
        return $ticket;
    }

    public function updateProcessingAgent($id, $processingAgentId)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $agent = $this->userRepo->findOneByGuid($processingAgentId);
        if ($agent && $agent['isAgent']) {
            $this->ticketRepo->update([['id', $ticket['id']]], ['processingAgentId' => $agent['id']]);
            $this->notificationRepo->sendNotification([
                'userId' => auth()->user()->id,
                'ticketId' => $ticket['id'],
                'description' => 'Ticket assigned to ' . $agent['firstname'] . ' ' . $agent['lastname'],
                'type' => 'assign',
            ]);
            return $ticket->refresh();
        }
        return $ticket;
    }

    public function removeAffiliateReferance($id, $ip)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        Log::info(sprintf(
            'ticketAffiliateReferenceRemove for ticket <%d> with affiliateId <%d> and affiliateReferenceNumber <%s> initiated by agent with IP <%s> and ID <%d>',
            $ticket['id'],
            $ticket['affiliateId'],
            $ticket['affiliateReferenceNumber'],
            $ip,
            auth()->user()->id
        ));
        $this->ticketRepo->update([['id', $id]], ['affiliateReferenceNumber' => null, 'affiliateId' => null]);
        return $ticket->refresh();
    }

    public function addAffiliateReferance($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $usedByTicket = $this->ticketRepo->findOneByAffiliateReferance($data['affiliateReferenceNumber']);
        if ($usedByTicket && $usedByTicket['id'] != $ticket['id']) {
            throw ValidationException::withMessages([
                'affiliateReferenceNumber' => [sprintf("This reference number is already assigned to ticket #%s", $this->ticketRepo->getTicketNumberFromId($usedByTicket['id']))]
            ]);
        }
        $this->ticketRepo->update([['id', $ticket['id']]], [
            'affiliateId' => $data['affiliateId'],
            'affiliateReferenceNumber' => $data['affiliateReferenceNumber']
        ]);
        return $ticket->refresh();
    }

    public function addCarrierDetails($id, $data)
    {
        $update = [];
        $ticketConstants = config('constants.ticket');
        $affiliateConstants = config('constants.affiliate');
        $ticket = $this->ticketRepo->findOrFail($id);
        $user = $this->userRepo->findById($ticket['userid']);

        if ($data['SBentryNum']) {
            $usedByTicket = $this->ticketRepo->findOneBySBentryNum($data['SBentryNum']);
            if ($usedByTicket && $usedByTicket['id'] != $ticket['id']) {
                throw ValidationException::withMessages([
                    'SBentryNum' => [sprintf("This entry number is already assigned to ticket #%s", $this->ticketRepo->getTicketNumberFromId($usedByTicket['id']))]
                ]);
            }
        }

        if ($data['transport']) {
            $update['transport'] = $data['transport'];
            if ($data['transport'] == $ticketConstants['transport']['OCEAN']) {
                $update['isffiled'] = $data['isffiled'] ?? false;
                $update['isfFiledOn'] = $data['isfFiledOn'] ?? null;
            } else {
                $update['isffiled'] = null;
            }
        }

        $update['ISFConsolidator_id'] = $data['ISFConsolidator_id'];
        if ($data['ISFConsolidatorContact_id'] != null) {
            if ($data['ISFConsolidatorContact_id'] == 0) {
                $ffc = [];
                $ffc['isfConsolidatorId'] = $data['isfConsolidatorId'];
                $ffc['isfcMobilePhone'] = $data['isfcMobilePhone'];
                $ffc['isfcEmailAddress'] = $data['isfcEmailAddress'];
                $ffc = $this->ffcRepo->create($ffc);
                $this->$data['ISFConsolidatorContact_id'] = $ffc['id'];
            }
            $update['ISFConsolidatorContact_id'] = $data['ISFConsolidatorContact_id'];
        }

        $update['carrier'] = $data['carrier'];
        $update['vendor_carrier_ref'] = $data['vendor_carrier_ref'];
        $update['requiresDelivery'] = $data['requiresDelivery'];
        $update['requiresLiftGate'] = $data['requiresLiftGate'];
        $update['deliveryAddress'] = $data['deliveryAddress'];
        $update['deliverySelection'] = $data['deliverySelection'];
        $update['haveLoadingDock'] = $data['haveLoadingDock'];

        $update['SBentryNum'] = $data['SBentryNum'] ??= null;
        $update['SBfilerCode'] = $data['SBfilerCode'] ??= null;

        $this->ticketRepo->update([['id', $ticket['id']]], $update);
        if ($data['isffiled'] && !$ticket['isffiled']) {
            $isFreightosTicket = $this->affiliateRepo->isFreightosTicket($ticket['affiliateId']);
            if ($isFreightosTicket) {
                // TODO: make api call to sevice
            } else {
                // Mail::to($user['email'])
                Mail::to('sandeepd.test@gmail.com')
                    ->send(new IsfFiled);
            }
        }
        return $ticket->refresh();
    }

    public function addEta($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $ticketNumber = $this->ticketRepo->getTicketNumberFromId($ticket['id']);
        $user = $this->userRepo->findById($ticket['userid']);
        $currentUser = auth()->user();
        $update = [];
        if ($data['eta']) $update['eta'] = (new Carbon($data['eta']))->format('Y-m-d H:i:s');
        if ($data['lastFreeDay']) $update['lastFreeDay'] = (new Carbon($data['lastFreeDay']))->format('Y-m-d H:i:s');
        $update['etaComment'] = $data['etaComment'];
        $update['disableEtaEmails'] = $data['disableEtaEmails'];
        $update['containerNumber'] = $data['containerNumber'];
        $update['mBOL'] = $data['mBOL'];
        $update['hBOL'] = $data['hBOL'];
        if (
            !$update['disableEtaEmails'] &&
            ($update['eta'] || $update['lastFreeDay']) &&
            ($ticket['eta'] != $update['eta'] || $ticket['lastFreeDay'] != $data['lastFreeDay'])
        ) {
            $mailData = [
                'ticketNumber' => $ticketNumber,
                'firstname' => $user['firstname'],
                'etaComment' => $update['etaComment'],
                'etaDate' => '',
                'etaTime' => '',
                'lastFreeDay' => '',
            ];
            try {
                if ($update['eta']) {
                    $etaDate = new Carbon($update['eta']);
                    $mailData['etaDate'] = $etaDate->format('F d, Y');
                    $mailData['etaTime'] = $etaDate->format('H:i');
                }
                if ($update['lastfreeday']) {
                    $mailData['lastFreeDay'] = (new Carbon($update['lastFreeDay']))->format('F d, Y');
                }
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }

            // Mail::to($user['email'])
            Mail::to('sandeepd.test@gmail.com')
                ->send(new EtaUpdated($mailData));

            $message = sprintf("<p>Dear %s,</p><p>There has been an update to the Estimated Time of Arrival or Last Free Day on your ticket.</p><p>Please find the most recent information below:</p>", $user['firstname']);
            if ($update['eta']) $message .= sprintf("<div><strong>ETA:</strong> %s</div>", $update['eta']);
            if ($update['lastFreeDay']) $message .= sprintf("<div><strong>Last Free Day:</strong> %s</div>", $update['lastFreeDay']);
            if ($update['etaComment']) $message .= sprintf("<div><strong>Comments:</strong> %s</div>", $update['etaComment']);

            $this->messageRepo->create([
                'guid' => Str::upper(Str::uuid()),
                'message' => $message,
                'ticketId' => $ticket['id'],
                'userid' => $currentUser['id'],
                'isMaster' => $update['isMaster']
            ]);
        }

        if ($update['containerNumber'] && !$ticket['vizionCreatedOn']) {
            // TODO: make external api call
        }
        $this->ticketRepo->update([['id', $ticket['id']]], $update);
        return $ticket->refresh();
    }

    /**
     * // TODO: Payment is pending and move mails into events. 
     */
    public function addBilling($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $ticketNumber = $this->ticketRepo->getTicketNumberFromId($ticket['id']);
        $user = $this->userRepo->findById($ticket['userid']);
        $ticketConstants = config('constants.ticket');
        $affiliateConstants = config('constants.affiliate');
        $isFreightosTicket = $this->affiliateRepo->isFreightosTicket($ticket['affiliateId']);
        $amount = floatval($data['amount']);

        $update = [];
        if (!$data['transactionNumber'] && !$ticket['amount'] && $data['amount']) {
            throw ValidationException::withMessages([
                'transactionNumber' => sprintf("Please specify an Invoice Number")
            ]);
        }

        if ($data['transactionNumber']) {
            $update['transactionNumber'] = $data['transactionNumber'];
        }

        if ($amount) {
            if ($data['doNotCharge']) {
                Log::info(sprintf("Do not charge ticket with ID %s", $ticket['id']));
            }

            if (!$ticket['amount']) {
                if ($amount < 0) {
                    throw new Exception(sprintf("Negative charge is now allowed as first ticket payment"));
                }

                $update['amount'] = $amount;

                $invoicePath = null;
                if ($data['invoice']) {
                    $fileName = Storage::putFile(Ticket::INVOICEPATH, $data['invoice']);
                    $update['fl_in'] = Ticket::INVOICEPATH . '/' . $fileName;
                    $invoicePath = Storage::disk('storage')->path($update['fl_in']);
                }

                $update['specialNotes'] = $data['specialNotes'];
                if ($ticket['isPaid'] == 1) {
                    Mail::to('sandeepd.test@gmail.com')
                        // Mail::to($user['email'])
                        ->send(new NewTicketPaid([
                            'specialNotes' => $data['specialNotes'],
                            'amount' => $amount,
                        ]));
                } else {
                    $payments = $this->paymentRepo->findByUserId($ticket['userid']);
                    if (count($payments) && !$data['doNotCharge']) {
                        $ticket['amount'] = $amount;
                        // TODO: make payment
                        if (false) {
                            // Mail::to($user['email'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new NewTicketCharge([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'invoicePath' => $invoicePath
                                ]));
                        } else {
                            $update['status'] = $ticketConstants['status']['NOT_PAID'];
                        }
                    } else {
                        $update['status'] = $ticketConstants['status']['NOT_PAID'];
                        if (!$data['doNotCharge']) {
                            // Mail::to($user['email'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new NewTicketChargeNoPayment([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'invoicePath' => $invoicePath,
                                    'amount' => $amount,
                                    'ticketNumber' => $ticketNumber,
                                    // TODO: add payment link
                                    'url' => ''
                                ]));
                        } else {
                            if ($isFreightosTicket) {
                                // Mail::to($affiliate['notificationEmail'])
                                Mail::to('sandeepd.test@gmail.com')
                                    ->send(new FreightosTicketInvoiceCreated([
                                        'amount' => $amount,
                                        'ticketNumber' => $ticketNumber,
                                        'customerName' => $user['firstname'] . ' ' . $user['lastname'],
                                        'affiliateReferenceNumber' => $ticket['affiliateReferenceNumber']
                                    ]));
                            } else {
                                // Mail::to($user['email'])
                                Mail::to('sandeepd.test@gmail.com')
                                    ->send(new NewTicketNoCharge([
                                        'specialNotes' => $data['specialNotes'],
                                        'transactionNumber' => $data['transactionNumber'],
                                        'invoicePath' => $invoicePath,
                                        'amount' => $amount,
                                        'ticketNumber' => $ticketNumber,
                                    ]));
                            }
                        }
                    }
                }
            } else {
                $sep = ';';
                $invoicePath = null;
                if ($data['invoice']) {
                    $fileName = Storage::putFile(Ticket::IADDITIONALPATH, $data['invoice']);
                    $filePath = Ticket::IADDITIONALPATH . '/' . $fileName;
                    $invoicePath = Storage::disk('storage')->path($filePath);
                    if ($ticket['paymentAmount']) {
                        $update['paymentFile'] = $ticket['paymentFile'] . $sep . $filePath;
                    } else {
                        $update['paymentFile'] = $filePath;
                    }
                } else {
                    if ($ticket['paymentAmount']) {
                        $update['paymentFile'] = $ticket['paymentFile'] . $sep;
                    } else {
                        $update['paymentFile'] = '';
                    }
                }
                $payments = $this->paymentRepo->findByUserId($ticket['userid']);
                if (count($payments) && !$data['doNotCharge']) {
                    $is_minus = false;
                    if ($amount > 0) {
                        // TODO: make api call
                        $result = true;
                    } else {
                        $is_minus = $result = false;
                    }

                    if ($result) {
                        if (!$is_minus) {
                            // Mail::to($user['email'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new NewAdditionalCard([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'invoicePath' => $invoicePath,
                                    'amount' => $amount,
                                ]));

                            // Mail::to(config('constants.config.INFO_EMAIL'))
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new NewCardPayment([
                                    'login' => $user['login'],
                                    'ticketNumber' => $ticketNumber,
                                    'amount' => $amount,
                                ]));
                        } else {
                            // Mail::to($email['value'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new RefundRequest([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'amount' => $amount,
                                ]));
                        }

                        $unpaidAdditionalAmount = $this->ticketRepo->getAmountMoney($ticket, false, 1);
                        if ($unpaidAdditionalAmount <= 0) {
                            $update['isPaidAdditional'] = false;
                        }
                        $isPaid = 1;
                    } else {
                        if (!$is_minus) {
                            // Mail::to($user['email'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new NewAdditionalNoCard([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'invoicePath' => $invoicePath,
                                    'amount' => $amount,
                                    // TODO: add payment link
                                    'url' => ''
                                ]));
                            // Mail::to(config('constants.config.INFO_EMAIL'))
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new NewCardPaymentDecline([
                                    'login' => $user['login'],
                                    'ticketNumber' => $ticketNumber,
                                    'amount' => $amount,
                                ]));
                        } else {
                            // Mail::to($email['value'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new RefundRequest([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'amount' => $amount,
                                ]));
                        }
                        $ticketUpdate['isPaidAdditional'] = true;
                        $isPaid = 0;
                    }
                } else {
                    $is_minus = false;
                    if ($amount < 0) $is_minus = true;
                    if (!$data['doNotCharge']) {
                        if (!$is_minus) {
                            // Mail::to($user['email'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new NewAdditionalNoCard([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'invoicePath' => $invoicePath,
                                    'amount' => $amount,
                                    // TODO: add payment link
                                    'url' => ''
                                ]));
                        } else {
                            // Mail::to($email['value'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new RefundRequest([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'amount' => $amount,
                                ]));
                        }
                    } else {
                        if (!$is_minus) {
                            if ($isFreightosTicket) {
                                // Mail::to($affiliate['notificationEmail'])
                                Mail::to('sandeepd.test@gmail.com')
                                    ->send(new FreightosTicketInvoiceCreated([
                                        'amount' => $amount,
                                        'ticketNumber' => $ticketNumber,
                                        'customerName' => $user['firstname'] . ' ' . $user['lastname'],
                                        'affiliateReferenceNumber' => $ticket['affiliateReferenceNumber']
                                    ]));
                            } else {
                                // Mail::to($user['email'])
                                Mail::to('sandeepd.test@gmail.com')
                                    ->send(new NewTicketNoCharge([
                                        'specialNotes' => $data['specialNotes'],
                                        'transactionNumber' => $data['transactionNumber'],
                                        'invoicePath' => $invoicePath,
                                        'amount' => $amount,
                                        'ticketNumber' => $ticketNumber,
                                    ]));
                            }
                        } else {
                            // Mail::to($email['value'])
                            Mail::to('sandeepd.test@gmail.com')
                                ->send(new RefundRequest([
                                    'specialNotes' => $data['specialNotes'],
                                    'transactionNumber' => $data['transactionNumber'],
                                    'amount' => $amount,
                                ]));
                        }
                    }
                    $ticketUpdate['isPaidAdditional'] = true;
                    $isPaid = 0;
                }
                $specialNotes = str_replace(';', ':', $data['specialNotes']);
                if (empty($ticket['paymentAmount'])) {
                    $update['paymentItem'] = $specialNotes;
                    $update['paymentAmount'] = $amount;
                    $update['paymentStatus'] = $isPaid + 1;
                } else {
                    $update['paymentItem'] = $ticket['paymentItem'] . $sep . $specialNotes;
                    $update['paymentAmount'] = $ticket['paymentAmount'] . $sep . $amount;
                    $update['paymentStatus'] = $ticket['paymentStatus'] . $sep . $isPaid + 1;
                }

                if ($isPaid) {
                    // Mail::to(config('constants.config.INFO_EMAIL'))
                    Mail::to('sandeepd.test@gmail.com')
                        ->send(new TicketNotify($this, $ticket));
                }
            }
        }

        $this->ticketRepo->update([['id', $ticket['id']]], $update);
        return $ticket->refresh();
    }

    public static function getFirstname($ticketOrId)
    {
        if (!is_object($ticketOrId)) {
            $ticketOrId = App::make(TicketRepository::class)->findOrFail($ticketOrId);
        }
        $ticketConstants = config('constants.ticket');
        $firstname = '';
        if ($ticketOrId['firstname']) {
            $firstname = $ticketOrId['firstname'];
        }
        if ($ticketOrId['type'] == $ticketConstants['type']['CLEARANCE'] && $ticketOrId['soldToId']) {
            $soldTo = App::make(UserSoldToRepository::class)->findByTicket($ticketOrId);
            if ($soldTo) {
                $firstname = $soldTo['firstname'];
            }
        }
        if (!$firstname) {
            $user = App::make(UserRepository::class)->findById($ticketOrId['userid']);
            if ($user) {
                $firstname = $user['firstname'];
            }
        }
        return $firstname;
    }

    public static function getLastname($ticketOrId)
    {
        if (!is_object($ticketOrId)) {
            $ticketOrId = App::make(TicketRepository::class)->findOrFail($ticketOrId);
        }
        $ticketConstants = config('constants.ticket');
        $lastname = '';
        if ($ticketOrId['lastname']) {
            $lastname = $ticketOrId['lastname'];
        }
        if ($ticketOrId['type'] == $ticketConstants['type']['CLEARANCE'] && $ticketOrId['soldToId']) {
            $soldTo = App::make(UserSoldToRepository::class)->findByTicket($ticketOrId);
            if ($soldTo) {
                $lastname = $soldTo['lastname'];
            }
        }
        if (!$lastname) {
            $user = App::make(UserRepository::class)->findById($ticketOrId['userid']);
            if ($user) {
                $lastname = $user['lastname'];
            }
        }
        return $lastname;
    }

    public static function getEmail($ticketOrId)
    {
        if (!is_object($ticketOrId)) {
            $ticketOrId = App::make(TicketRepository::class)->findOrFail($ticketOrId);
        }
        $ticketConstants = config('constants.ticket');
        $email = '';
        if ($ticketOrId['email']) {
            $email = $ticketOrId['email'];
        }
        if ($ticketOrId['type'] == $ticketConstants['type']['CLEARANCE'] && $ticketOrId['soldToId']) {
            $soldTo = App::make(UserSoldToRepository::class)->findByTicket($ticketOrId);
            if ($soldTo) {
                $email = $soldTo['email'];
            }
        }
        if (!$email) {
            $user = App::make(UserRepository::class)->findById($ticketOrId['userid']);
            if ($user) {
                $email = $user['email'];
            }
        }
        return $email;
    }

    public function markAsPaid($id, $payId)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $ticketConstants = config('constants.ticket');
        $update = [];
        if ($payId == 1) {
            $update['isPaid'] = true;
        }
        $stepCount = intval($payId) - 2;
        $paymentStatuses = explode(';', $ticket['paymentStatus']);
        foreach ($paymentStatuses as $index => $value) {
            if ($index == $stepCount) {
                $paymentStatuses[$index] = 2;
            }
        }

        $update['paymentStatus'] = implode(';', $paymentStatuses);
        $unpaidAdditionalAmount = $this->ticketRepo->getAmountMoney($ticket, false, 1);
        if ($unpaidAdditionalAmount <= 0) {
            $update['isPaidAdditional'] = 0;
        }
        $update['paidDate'] = Carbon::now();
        $update['status'] = $ticketConstants['status']['PAID'];
        $this->ticketRepo->update([['id', $ticket['id']]], $update);
        return $ticket->refresh();
    }

    public function removePayment($id, $payId)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $update = [];
        if ($payId == 1) {
            if (!$ticket['isPaid']) {
                throw new Exception("Payment is already processed! Can't remove it");
            }
            $update['amount'] = null;
            $update['specialNotes'] = null;
            $update['fl_in'] = null;
            $update['isPaid'] = false;
        } else {
            $sep = ';';
            $itemId = intval($payId) - 2;
            $amounts = explode($sep, $ticket['paymentAmount']);
            $statuses = explode($sep, $ticket['paymentStatus']);
            $files = explode($sep, $ticket['paymentFile']);
            $items = explode($sep, $ticket['paymentItem']);
            if (isset($amounts[$itemId])) {
                unset($amounts[$itemId]);
                unset($statuses[$itemId]);
                unset($files[$itemId]);
                unset($items[$itemId]);
            }
            $ticketUpdate['paymentAmount'] = implode($sep, $amounts);
            $ticketUpdate['paymentStatus'] = implode($sep, $statuses);
            $ticketUpdate['paymentFile'] = implode($sep, $files);
            $ticketUpdate['paymentItem'] = implode($sep, $items);
        }
        $this->ticketRepo->update([['id', $ticket['id']]], $update);
        return $ticket->refresh();
    }

    /**
     * @not implemented
     */
    public function addPgaRequest($id, $data)
    {
        // $ticket = $this->ticketRepo->findOrFail($id);
        // $pga = $this->pgaRepo->findOrFail($data['pgaId']);
        // $user = $this->userRepo->findById($ticket['userid']);
        // $currentUser = auth()->user();
        // $agentId = $currentUser['id'];
        // $pgaRequest = $this->pgaReqRepo->create([
        //     'pgaId' => $pga['id'],
        //     'ticketId' => $ticket['id'],
        //     'agentId' => $agentId,
        //     'note' => $data['note']
        // ]);
        // $res = $this->srService->getEmbeddedLink($pga['templateId'], $user['firstname'], '', 'User');
        // dd($res);
        // // TODO: sign document
        // $this->pgaReqRepo->update([['id', $pgaRequest['id']]], [
        //     'documentGuid' => '',
        //     'signerLink' => '',
        // ]);
        // $this->messageRepo->saveMessage([
        //     'guid' => Str::upper(Str::uuid()),
        //     'ticketId' => $ticket['id'],
        //     'userId' => $agentId,
        //     'isMaster' => true,
        //     'message' => sprintf('PGA is requested - Please <a href="%s" style="text-decoration:underline;"">click here</a> to Complete form and sign the document.', '')
        // ]);
        // $this->ticketRepo->update([['id', $ticket['id']]], ['isNew1' => $agentId]);
        // return $pgaRequest->refresh();
    }

    public function attachUserHts($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $uhts = $this->uhtsRepo->findOrFail($data['uhtsId']);
        $this->tuhtsRepo->create([
            'ticketId' => $ticket['id'],
            'userHtsId' => $uhts['id']
        ]);
        return $ticket->refresh();
    }

    public function sendNotifyTariffCodeEmail($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $user = $this->userRepo->findOneOrFailByGuid($data['guid']);
        $update = [];
        $update['tariffCodeEmailSent'] = Carbon::now();
        if ($data['role'] != '') {
            $userRole = $this->userRoleRepo->findOneByInteralKey($data['role']);
            $update['roleStatusId'] = DB::raw(UserRoleRepository::GetTicketRoleStatusFunction . "($id, {$userRole['id']})");
        }
        $this->ticketRepo->update([['id', $id]], $update);
        $ticket->refresh();
        event(new NotifyTariffCode($ticket, $user, auth()->user()));
        return $ticket;
    }

    public function updateNotifyTariffCode($id, $data)
    {
        $ticket = $this->ticketRepo->findOrFail($id);
        $update = [];
        $update['tariffCodeEmailSent'] = null;
        if ($data['role'] != '') {
            $userRole = $this->userRoleRepo->findOneByInteralKey($data['role']);
            $update['roleStatusId'] = DB::raw(UserRoleRepository::GetTicketRoleStatusFunction . "($id, {$userRole['id']})");
        }
        $this->ticketRepo->update([['id', $id]], $update);
        $ticket->refresh();
        return $ticket;
    }
}
