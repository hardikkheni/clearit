<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasGuidColumn;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TicketRepository extends BaseRepository
{
    use HasGuidColumn;

    const MODEL_LABEL = 'Ticket';

    protected $userRepo;
    protected $astRepo;
    protected $affiRepo;

    /**
     * TODO: remove repository injection in another repository.
     */
    public function __construct(
        Ticket $model,
        UserRepository $userRepo,
        AgentStatusTypeRepository $astRepo,
        AffiliateRepository $affiRepo
    ) {
        parent::__construct($model);
        $this->userRepo = $userRepo;
        $this->astRepo = $astRepo;
        $this->affiRepo = $affiRepo;
    }

    public function getUsedTicketStatusList()
    {
        $subquery = $this->model->query()
            ->select([DB::raw('DISTINCT status'), 'status as statusname'])
            ->toSql();
        return DB::query()->from(DB::raw("($subquery) as t"))
            ->leftJoin('ticket_status as ts', "ts.statusname", "t.status")
            ->groupBy('ts.statusname')->orderBy('ts.displayorder', 'ASC')
            ->select(['t.*', 'ts.hexcolor', 'ts.texthexcolor'])->get();
    }

    public function getTicketId($id)
    {
        return $id - 99999;
    }

    public static function getTicketNumberFromId($id)
    {
        return $id + 99999;
    }

    public function getTicketNameByUserId($ticket, $userId = null)
    {
        if (!$userId) $userId = $ticket['userid'];
        return $this->userRepo->getNameById($userId);
    }

    public function findOneByAffiliateReferance($referanceCode)
    {
        return $this->findOne([['affiliateReferenceNumber', $referanceCode]]);
    }

    public function findOneBySBentryNum($SBentryNum)
    {
        return $this->findOne([['SBentryNum', $SBentryNum]]);
    }

    /**
     *
     */
    public function getTicketListFiltersCount(
        $type,
        $status,
        $from,
        $to,
        $agent,
        $search,
        $transport = null,
        $agentStatusId = null,
        $includeDeleted = false,
        $affiliateId = null
    ) {
        $ticketConstants = config('constants.ticket');

        //Enable completed tickets for search and for user tickets
        if ($status == $ticketConstants['status']['OPEN'] && ($search || $agent)) {
            $status = $ticketConstants['status']['ALL'];
        }

        $query = $this->model->query();
        if (!$includeDeleted) {
            $query->withoutTrashed();
        }

        if ($type == 'new') {
            $query->where("(ticket.`status`=\"{$ticketConstants['status']['NEW']}\" or ticket.isnewticket=1)");
        } else if ($type == 'message') {
            $query->join('message as m', 'm.ticketid', 'ticket.id');
            $query->join('agent_message as am', 'am.messageid', 'm.id');
            $query->whereRaw('m.`ismaster` = 0');
            $query->whereRaw('am.readon IS NULL');
        } else if ($type == 'broker') {
            $query->whereRaw('ticket.`requires_broker_review` = 1');
        } else if ($type && trim(strtolower($type)) != 'all') {
            $query->whereRaw("ticket.`type`=\"$type\"");
        }

        if ($status && trim(strtolower($status)) != 'all' && $status != $ticketConstants['status']['OPEN']) {
            $query->whereRaw("ticket.`status`=\"$status\"");
        }

        if ($from && trim(strtolower($from)) != 'from:') {
            $query->whereRaw("ticket.`createdOn`>= \"$from\"");
        }
        if ($to && trim(strtolower($to)) != 'to:') {
            $query->whereRaw("ticket.`createdOn`<= \"$to\"");
        }

        if ($agent) {
            if (substr($agent, 0, 5) == 'CUST:') {
                $customerGuid = substr($agent, 5);
                $customer = $this->userRepo->findOneByGuid($customerGuid);
                if ($customer) {
                    $query->whereRaw("(ticket.`userid`=\"{$customer->id}\")");
                }
            } else {
                $user = $this->userRepo->findOneByGuid($agent);
                if ($user) {
                    $query->whereRaw("(ticket.`agentid`={$user->id} OR ticket.`processingagentid`={$user->id})");
                }
            }
        }

        if ($search && is_numeric($search) && strlen($search) <= 7) {
            $id = $this->getTicketId($search);
            $query->whereRaw("(ticket.`id`=\"$id\"  OR ticket.`transactionnumber` like \"%$search%\" OR ticket.`affiliatereferencenumber` like \"%$search%\")");
        } else if ($search) {
            $query->leftJoin('user as u', 'u.id', 'ticket.userid');
            $query->leftJoin('user_sold_to as sold_to', 'ticket.soldtoid', 'sold_to.id');
            $query->whereRaw('(ticket.`vendor_carrier_ref` like "%' . $search . '%" OR ticket.`firstname` like "%' . $search . '%" OR ticket.`lastname` like "%' . $search . '%" OR ticket.`email` like "%' . $search . '%" OR ticket.`transactionnumber` like "%' . $search . '%" OR ticket.`busname` like "%' . $search . '%" OR u.`busname` like "%' . $search . '%" OR u.`tradename` like "%' . $search . '%" OR u.`email` like "%' . $search . '%" OR CONCAT(ticket.`lastname`," ",ticket.`firstname`) like "%' . $search . '%" OR sold_to.`busname` like "%' . $search . '%" OR sold_to.`firstname` like "%' . $search . '%" OR sold_to.`lastname` like "%' . $search . '%" OR sold_to.`email` like "%' . $search . '%" OR ticket.`affiliatereferencenumber` like "%' . $search . '%" OR ticket.`mbol` like "%' . $search . '%" OR ticket.`hbol` like "%' . $search . '%" OR ticket.`containernumber` like "%' . $search . '%")');
            $query->whereRaw('(ticket.istemporary=0 AND ticket.isaccepted=1)');
        }

        if ($transport) {
            $query->whereRaw("ticket.`transport` = $transport");
        }
        if ($agentStatusId && $agentStatusId > 0) {
            $query->whereRaw("ticket.`agentStatusTypeId` = $agentStatusId");
        }

        if ($affiliateId > 0) {
            $query->whereRaw("ticket.`affiliateId` = $affiliateId");
        }

        if ($status == $ticketConstants['status']['OPEN']) {
            $query->whereRaw("ticket.`status` != \"{$ticketConstants['status']['COMPLETE']}\"");
        }

        $row = $query->select([DB::raw('COUNT(DISTINCT ticket.id)  as count')])->first();
        if ($row) return intval($row->count);
        return 0;
    }

    /**
     *
     * @return  array $entities
     */
    public function getFilteredTicketList(
        $type,
        $status,
        $from,
        $to,
        $agent,
        $search,
        $transport = null,
        $agentStatusId = null,
        $includeDeleted = false,
        $affiliateId = null,
        $offset = 0,
        $limit = 40
    ) {
        $ticketConstants = config('constants.ticket');
        $notificationConstants = config('constants.notification');
        if ($status == $ticketConstants['status']['OPEN'] && ($search || $agent)) {
            $status = $ticketConstants['status']['ALL'];
        }

        $agentId = auth()->user()->id;
        $is_front = 0;

        $hasUnreadMsgSql = "EXISTS (
            SELECT 1 FROM agent_message sub_am JOIN message sub_m ON sub_m.id = sub_am.messageid
            WHERE sub_m.ticketid = ticket.id AND sub_am.userid = $agentId
            AND sub_m.ismaster = $is_front AND sub_am.readon IS NULL
        ) AS has_unread_messages";

        if ($type == 'message') {
            // FIX for "View all" messages link - show messages to ALL agents
            $hasUnreadMsgSql = "EXISTS (
                SELECT 1 FROM agent_message sub_am JOIN message sub_m ON sub_m.id = sub_am.messageid
                WHERE sub_m.ticketid = ticket.id
                AND sub_m.ismaster = $is_front AND sub_am.readon IS NULL
            ) AS has_unread_messages";
        }

        $hasUnreadUploadNotifications = "EXISTS (
            SELECT 1 FROM agent_notification sub_an JOIN notification sub_n ON sub_n.id = sub_an.notificationid
            WHERE sub_n.ticketid = ticket.id AND sub_an.userid = $agentId
            AND sub_an.readon IS NULL AND (sub_n.type=\"upload\" OR sub_n.type=\"pga_sign\" OR sub_n.type= \"{$notificationConstants['type']['AFFILIATE_DOCUMENT_UPLOAD']}\")
        ) AS has_unread_upload_notifications";

        $query = $this->model->query()->select([DB::raw('DISTINCT ticket.*'), DB::raw($hasUnreadMsgSql), DB::raw($hasUnreadUploadNotifications)]);

        if (!$includeDeleted) {
            $query->withoutTrashed();
        }

        if ($type == 'new') {
            $query->whereRaw("(ticket.`status`= \"{$ticketConstants['status']['NEW']}\" or ticket.isnewticket=1)");
        } else if ($type == 'message') {
            $query->join('message as m', 'm.ticketid', 'ticket.id');
            $query->join('agent_message as am', 'am.messageid', 'm.id');
            $query->whereRaw("m.`ismaster` = $is_front");
            $query->whereRaw('am.readon IS NULL');
        } else if ($type == 'broker') {
            $query->whereRaw('ticket.`requires_broker_review` = 1');
        } else if ($type && trim(strtolower($type)) != 'all') {
            $query->whereRaw("ticket.`type`=\"$type\"");
        }
        if ($status && trim(strtolower($status)) != 'all' && $status != $ticketConstants['status']['OPEN']) {
            $query->whereRaw("ticket.`status`=\"$status\"");
        }
        if ($from && trim(strtolower($from)) != 'from:') {
            $query->whereRaw('ticket.`createdOn`>="' . $from . '"');
        }
        if ($to && trim(strtolower($to)) != 'to:') {
            $query->whereRaw('ticket.`createdOn`<="' . $to . '"');
        }
        if ($agent) {
            if (substr($agent, 0, 5) == 'CUST:') {
                $customerGuid = substr($agent, 5);
                $customer = $this->userRepo->findOneByGuid($customerGuid);
                if ($customer) {
                    $query->whereRaw('(ticket.`userid`="' . $customer['id'] . '")');
                }
            } else {
                $user = $this->userRepo->findOneByGuid($agent);
                if ($user) {
                    $query->whereRaw('(ticket.`agentid`="' . $user['id'] . '" OR ticket.`processingagentid`="' . $user['id'] . '")');
                }
            }
        }
        if ($search && is_numeric($search) && strlen($search) <= 7) {
            $id = $this->getTicketId($search);
            $query->whereRaw('(ticket.`id`="' . $id . '"  OR ticket.`transactionnumber` like "%' . $search . '%" OR ticket.`affiliatereferencenumber` like "%' . $search . '%")');
        } else if ($search) {
            $query->leftJoin('user as u', 'u.id', 'ticket.userid');
            $query->leftJoin('user_sold_to as sold_to', 'ticket.soldtoid', 'sold_to.id');
            $query->whereRaw('(ticket.`vendor_carrier_ref` like "%' . $search . '%" OR ticket.`firstname` like "%' . $search . '%" OR ticket.`lastname` like "%' . $search . '%" OR ticket.`email` like "%' . $search . '%" OR ticket.`transactionnumber` like "%' . $search . '%" OR ticket.`busname` like "%' . $search . '%" OR u.`busname` like "%' . $search . '%" OR u.`tradename` like "%' . $search . '%" OR u.`email` like "%' . $search . '%" OR CONCAT(ticket.`lastname`," ",ticket.`firstname`) like "%' . $search . '%" OR sold_to.`busname` like "%' . $search . '%" OR sold_to.`firstname` like "%' . $search . '%" OR sold_to.`lastname` like "%' . $search . '%" OR sold_to.`email` like "%' . $search . '%" OR ticket.`affiliatereferencenumber` like "%' . $search . '%" OR ticket.`mbol` like "%' . $search . '%" OR ticket.`hbol` like "%' . $search . '%" OR ticket.`containernumber` like "%' . $search . '%")');
        }
        $query->whereRaw('(ticket.istemporary=0 AND ticket.isaccepted=1)');
        if ($transport) {
            $query->whereRaw('ticket.`transport` = ' . $transport);
        }
        if ($agentStatusId && $agentStatusId > 0) {
            $query->whereRaw('ticket.`agentStatusTypeId` = ' . $agentStatusId);
        }
        if ($affiliateId > 0) {
            $query->whereRaw('ticket.`affiliateId` = ' . $affiliateId);
        }
        if ($status == $ticketConstants['status']['OPEN']) {
            $query->whereRaw('ticket.`status` != "' . $ticketConstants['status']['COMPLETE'] . '"');
        }

        $query->orderBy('has_unread_messages', 'DESC');
        $query->orderBy('has_unread_upload_notifications', 'DESC');
        $query->orderBy('ticket.id', 'DESC');

        $rows = $query->limit($limit)->offset($offset)->get()->toArray();
        $entities = [];
        foreach ($rows as $key => $row) {
            $userid = $row['userid'];
            $agentid = $row['agentid'];
            $processingAgentId = $row['processingAgentId'];
            $user = $this->userRepo->findById($userid);
            $row['email_user'] = $user['email'];
            $row['isCertificate'] = $user['isCertificate'];
            $row['firstname_user'] = $user['firstname'];
            $row['lastname_user'] = $user['lastname'];
            $row['busname_user'] = $user['busname'];
            $row['tradename_user'] = $user['tradename'];
            $row['status_user'] = $user['status'];
            $row['agent_name'] = '';
            $row['processing_agent_name'] = '';
            $row['affiliate_company_name'] = '';
            if ($agentid) {
                $agent = $this->userRepo->findById($agentid);
                if ($agent) {
                    $row['agent_name'] = $agent['firstname'] . ' ' . $agent['lastname'];
                }
            }
            if ($processingAgentId) {
                $processingAgent = $this->userRepo->findById($processingAgentId);
                if ($processingAgent) {
                    $row['processing_agent_name'] = $processingAgent['firstname'] . ' ' . $processingAgent['lastname'];
                }
            }
            $agentStatusTypeName = '';
            if ($row['agentStatusTypeId']) {
                $agentStatusType = $this->astRepo->findById($row['agentStatusTypeId']);
                if ($agentStatusType) {
                    $agentStatusTypeName = $agentStatusType->statusname;
                }
            }
            $row['agent_status_type'] = $agentStatusTypeName;

            $ticketAffiliate = $this->affiRepo->findById((int) $row['affiliateId']);
            if ($ticketAffiliate && $ticketAffiliate->companyname) {
                $row['affiliate_company_name'] = $ticketAffiliate->companyname;
            }
            $entities[] = $row;
        }
        return $entities;
    }

    public function getFreightosBilling($isBilled, $from, $to)
    {
        $data = $this->call('getFreightosBilling', [$isBilled, $from, $to]);
        return $data[0] ?? [];
    }

    public function resetNew($id)
    {
        return $this->model->where(['isNew' => true, 'id' => $id])->update(['isNew' => false]);
    }

    public function getFullTicket($ticketId, $userId, $roleId)
    {
        $result = [];
        $data = $this->call('getFullTicket', [$ticketId, $userId, $roleId]);
        $result = (array) ($data[0] ?? [[]])[0];
        $result['agent_status'] = $data[1] ?? [];
        $result['ticket_documents'] = $data[2] ?? [];
        $result['to_do_checklist'] = $data[3] ?? [];
        $result['hts_code_assignment'] = $data[4] ?? [];
        $result['client_requests_data'] = $data[5] ?? [];
        $result['reminder_items_data'] = $data[6] ?? [];
        $result['customer_sold_vendor_data'] = $data[7] ?? [];
        $result['affiliates_detail'] = $data[8] ?? [];
        $result['freight_forwarders_data'] = $data[9] ?? [];
        $result['freight_forwarders_contact_data'] = $data[10] ?? [];
        $result['notification_data'] = $data[11] ?? [];
        return $result;
    }

    public function getTicketNotifications($ticketId)
    {
        return $this->find(['ticketId' => $ticketId]);
    }

    public function getTicketSBmessageHistoryByTicket($ticket)
    {
        return DB::table('sb_posts')->where('SBnum', $ticket['SBnum'])->get();
    }

    public function getFcInvoiceDatetime($id)
    {
        return $this->model->select([
            DB::raw('DATE_FORMAT(MAX(freightos_billing_sent), \'%M %D, %Y\') as invoice_date'),
            DB::raw("DATE_FORMAT(getAdjustedDatetime(freightos_billing_sent, $id), '%l:%i%p') as invoice_format_time")
        ])->find(+$id);
    }

    public function delete($ticketOrId, $reason)
    {
        if (!is_object($ticketOrId)) {
            $ticketOrId = $this->findOrFail($ticketOrId);
        }
        $isDeleted = $this->update([['id', $ticketOrId->id]], [
            'isDeleted' => true,
            'deletedOn' => Carbon::now()->format('Y-m-d H:i:s'),
            'deletedByUserId' => auth()->user()->id,
            'deletionReason' => $reason
        ]);
        if ($isDeleted) return $ticketOrId->refresh();
        return null;
    }

    public function deleteTicketUnpaidAmount($ticketOrId)
    {
        if (!is_object($ticketOrId)) {
            $ticketOrId = $this->findOrFail($ticketOrId);
        }
        $delim          = ';';
        $paymentItems   = explode($delim, $ticketOrId['paymentItem']);
        $paymentAmounts = explode($delim, $ticketOrId['paymentAmount']);
        $paymentStatus  = explode($delim, $ticketOrId['paymentStatus']);
        $paymentFiles   = explode($delim, $ticketOrId['paymentFile']);
        $isPaymentsRemoved = false;
        $isTicketUnpaid = !$ticketOrId['isPaid'] && $ticketOrId['amount'] > 0;

        foreach ($paymentItems as $f => $item) {
            $status = $paymentStatus[$f];
            // 2 - it is paid
            if ($status != 2) {
                if (isset($paymentAmounts[$f])) {
                    unset($paymentAmounts[$f]);
                }
                if (isset($paymentStatus[$f])) {
                    unset($paymentStatus[$f]);
                }
                if (isset($paymentFiles[$f])) {
                    unset($paymentFiles[$f]);
                }
                unset($paymentItems[$f]);
                $isPaymentsRemoved = true;
            }
            unset($status);
        }

        if (!$isPaymentsRemoved && !$isTicketUnpaid) {
            return true;
        }
        $data = array();
        if ($isPaymentsRemoved) {
            $data['paymentItem']   = implode($delim, $paymentItems);
            $data['paymentAmount'] = implode($delim, $paymentAmounts);
            $data['paymentStatus'] = implode($delim, $paymentStatus);
            $data['paymentFile']   = implode($delim, $paymentFiles);
        }
        if ($isTicketUnpaid) {
            $data['amount'] = 0;
        }
        $this->update([['id', $ticketOrId['id']]], $data);
        return $ticketOrId;
    }

    public function getArrivalNoticeTickets($isVerified, $offset, $limit)
    {
        return $this->call('getArrivalNoticeTickets', [$isVerified, $offset, $limit]);
    }
    public function getPrekeyBillingTickets($offset, $limit)
    {
        return $this->call('getPrekeyBillingTickets', [$offset, $limit]);
    }
    public function getReleaseTeamTickets($offset, $limit)
    {
        return $this->call('getReleaseTeamTickets', [$offset, $limit]);
    }
    public function getIsfTickets($offset, $limit)
    {
        return $this->call('getISFTickets', [$offset, $limit]);
    }
    public function getISFNotFiled($isVerified, $offset, $limit)
    {
        return $this->call('getISFNotFiled', [$isVerified, $offset, $limit]);
    }

    public function getAmountMoney($ticketOrId, $isPaid = true, $isAdditional = 0)
    {
        if (!is_object($ticketOrId)) {
            $ticketOrId = $this->findOrFail($ticketOrId);
        }

        $paid = 0;
        $unpaid = 0;
        $additionalPaid = 0;
        $additionalUnpaid = 0;
        $additional = 0;

        $delim = ';';
        $paymentItems = $ticketOrId['paymentItem'] ? explode($delim, $ticketOrId['paymentItem']) : [];
        $paymentAmounts = $ticketOrId['paymentAmount'] ? explode($delim, $ticketOrId['paymentAmount']) : [];
        $paymentStatuses = $ticketOrId['paymentStatus'] ? explode($delim, $ticketOrId['paymentStatus']) : [];

        for ($f = 0; $f < count($paymentItems); $f++) {
            $amount = $paymentAmounts[$f];
            $status = $paymentStatuses[$f];
            // 2 - it is paid additional charge
            if ($status == 2) {
                $paid += $amount;
                $additionalPaid += $amount;
            } else {
                $unpaid += $amount;
                $additionalUnpaid += $amount;
            }
            $additional += $amount;
        }
        if ($ticketOrId['isPaid'] == 1) {
            $paid += $ticketOrId['amount'];
        } else {
            $unpaid += $ticketOrId['amount'];
        }
        if ($isAdditional == 2) {
            return $additional;
        }
        if ($isAdditional == 1) {
            if ($isPaid) return $additionalPaid;
            else return $additionalUnpaid;
        } else {
            if ($isPaid) return $paid;
            else return $unpaid;
        }
    }
}
