<?php

namespace App\Services;

use App\Repositories\Eloquent\{
    AffiliateRepository,
    AgentStatusTypeRepository,
    NoteRepository,
    PaymentRepository,
    TicketRepository,
    TicketStatusRepository,
    UserRepository,
    UserRoleRepository,
};
use Illuminate\Validation\UnauthorizedException;

class AuthService
{

    protected $userRepo;
    protected $userRoleRepo;
    protected $agentStatusTypeRepo;
    protected $ticketRepo;
    protected $affiliateRepo;
    protected $paymentRepo;
    protected $noteRepo;
    protected $tsRepo;

    public function __construct(
        UserRepository $userRepo,
        UserRoleRepository $userRoleRepo,
        AgentStatusTypeRepository $agentStatusTypeRepo,
        TicketRepository $ticketRepo,
        AffiliateRepository $affiliateRepo,
        PaymentRepository $paymentRepo,
        NoteRepository $noteRepo,
        TicketStatusRepository $tsRepo
    ) {
        $this->userRepo = $userRepo;
        $this->userRoleRepo = $userRoleRepo;
        $this->agentStatusTypeRepo = $agentStatusTypeRepo;
        $this->ticketRepo = $ticketRepo;
        $this->affiliateRepo = $affiliateRepo;
        $this->paymentRepo = $paymentRepo;
        $this->noteRepo = $noteRepo;
        $this->tsRepo = $tsRepo;
    }

    public function login($data, $ip = null)
    {
        $user = $this->userRepo->findByEmailOrLogin($data['username'])->makeVisible(['password']);
        if ($user->getAuthPassword() !== md5($data['password'])) {
            throw new UnauthorizedException("Invalid login details!.");
        }
        $token = $user->createToken($ip)->plainTextToken;
        unset($user->password);
        return [
            'access_token' => $token,
            'user' => $user,
        ];
    }

    public function dashboard($data)
    {
        $user = auth()->user();
        $ticketConstants = config('constants.ticket');
        $data['userId'] = $user->id;
        $data['roleBitmask'] = $user->roleBitmask;
        $data['role'] ??= 'docreview';
        $data['type'] ??= $ticketConstants['type']['ALL'];
        $data['status'] ??= $ticketConstants['status']['ALL'];
        $data['from'] ??= 'From:';
        $data['to'] ??= 'To:';
        $data['agent'] ??= 0;
        $data['transport'] = $data['type'] == $ticketConstants['type']['CLEARANCE'] ? $data['transport'] ??= 0 : 0;
        $data['agent_status'] ??= 0;
        $data['affiliateId'] ??= 0;
        $data['search'] ??= '';

        $data['page'] ??= 1;
        $data['limit'] = 30;

        if ($data['role'] == 'master') {
            return $this->getMasterData($data);
        } else {
            return $this->getDashboardData($data);
        }
    }

    public function getMasterData($data)
    {
        $ticketConstants = config('constants.ticket');
        $affiliateConstants = config('constants.affiliate');
        $agentStatusTypes = $this->agentStatusTypeRepo->getUsedAgentStatusTypeList();
        $ticketStatusList = $this->ticketRepo->getUsedTicketStatusList()->toArray();
        $freightosAffiliate = $this->affiliateRepo->getAffiliateByCode($affiliateConstants['FREIGHTOSCODE']);
        $freightosId = $freightosAffiliate['id'] ?? 0;
        $alibabaAffiliate = $this->affiliateRepo->getAffiliateByCode($affiliateConstants['ALIBABACODE']);
        $alibabaId = $alibabaAffiliate['id'] ?? 0;

        $includeDeleted = $data['search'] ? true : false;

        array_unshift($ticketStatusList, (object) [
            'status' => $ticketConstants['status']['ALL'],
            'statusname' => $ticketConstants['status']['OPEN'],
            'hexcolor' => $ticketConstants['DEFAULT_HEX_COLOR'],
            'texthexcolor' => $ticketConstants['DEFAULT_TEXT_HEX_COLOR'],
        ]);

        foreach ($ticketStatusList as $key => $ticketStatus) {
            $statusName = $ticketStatus->statusname;
            $count = $this->ticketRepo->getTicketListFiltersCount(
                $data['type'],
                $statusName,
                $data['from'],
                $data['to'],
                $data['agent'],
                $data['search'],
                $data['transport'],
                $data['agent_status'],
                $includeDeleted,
                $freightosId
            );
            $ticketStatusList[$key]->count = $count;
        }

        $listStatus = $data['status'];
        if ($data['status'] == $ticketConstants['status']['ALL']) {
            $listStatus = $ticketConstants['status']['OPEN'];
        }

        $list = $this->ticketRepo->getFilteredTicketList(
            $data['type'],
            $listStatus,
            $data['from'],
            $data['to'],
            $data['agent'],
            $data['search'],
            $data['transport'],
            $data['agent_status'],
            $includeDeleted,
            $freightosId,
            ($data['page'] - 1) * $data['limit'],
            $data['limit'],
        );

        $count = $this->ticketRepo->getTicketListFiltersCount(
            $data['type'],
            $listStatus,
            $data['from'],
            $data['to'],
            $data['agent'],
            $data['search'],
            $data['transport'],
            $data['agent_status'],
            $includeDeleted,
            $freightosId
        );
        foreach ($list as $key => $row) {
            $list[$key]['ispga'] = 0;
            $list[$key]['isCertificate'] = false;
            $list[$key]['isReference'] = false;
            $list[$key]['isPoa'] = false;
            $list[$key]['isCc'] = false;
            $list[$key]['client_guid'] = '';
            $list[$key]['agent_guid'] = '';
            $list[$key]['client_guid'] = '';

            $user = null;
            if ($row['userid']) {
                $user = $this->userRepo->findById($row['userid']);
            }

            if ($user) {
                $payment = $this->paymentRepo->findOneByUserId($row['userid']);
                if ($payment) $list[$key]['isCc'] = true;
                $list[$key]['isReference'] = $user->isReference;
                $list[$key]['isVerified'] = $user->isVerified;
                $list[$key]['isCertificate'] = $user->isCertificate;
                $list[$key]['isPoa'] = $user->isSigned;
                $list[$key]['client_guid'] = $user->guid;
            }

            // check notes
            $note = $this->noteRepo->findOneByTicketId($row['id']);
            // $note = $this->noteRepo->findOneByGuid($row['id']);
            if ($note) {
                if ($user) {
                    $user_format = $user['firstname'] . ' at ';
                } else {
                    $user_format = '';
                }
                $date_format = date('d-m-Y h:i a', strtotime($note['createdon']));
                $note['description'] = strip_tags(html_entity_decode($note['description']));
                $list[$key]['note_user'] = $user_format . $date_format . '<br>' . (strlen($note['description']) > 150 ? substr($note['description'], 0, 150) . '...' : $note['description']);
            } else {
                $list[$key]['note_user'] = '';
            }

            if ($row['agentid']) {
                $agent_user = $this->userRepo->findById($row['agentid']);
                $list[$key]['agent_guid'] = $agent_user['guid'];
            } else {
                $list[$key]['agent_guid'] = null;
            }
            if ($row['processingAgentId']) {
                $processing_agent_user = $this->userRepo->findById($row['processingAgentId']);
                $list[$key]['processing_agent_guid'] = $processing_agent_user['guid'];
            } else {
                $list[$key]['processing_agent_guid'] = null;
            }
            $list[$key]['ismessage'] = $row['isNew'];
            $list[$key]['hexcolor'] = $this->tsRepo->getHexColorByTicket($row);
            $list[$key]['texthexcolor'] = $this->tsRepo->getTextHexColorByTicket($row);
            $list[$key]['ticket_name'] = $this->ticketRepo->getTicketNameByUserId($row);
        }
        $agents = $this->userRepo->getUsedAgentList();
        $affiliates = $this->affiliateRepo->getAffiliateList();

        $user = auth()->user();
        $roles = $this->userRoleRepo->dashboardRoles($user->roleBitmask);

        return [
            'roles' => $roles,
            'agentStatusTypes' => $agentStatusTypes,
            'ticketStatusList' => $ticketStatusList,
            'alibabaId' => $alibabaId,
            'freightosId' => $freightosId,
            'list' => $list,
            'agents' => $agents,
            'affiliates' => $affiliates,
            'type' => $data['type'],
            'status' => $data['status'],
            'from' => $data['from'],
            'to' => $data['to'],
            'agent' => $data['agent'],
            'search' => $data['search'],
            'transport' => $data['transport'],
            'agent_status' => $data['agent_status'],
            'page' => $data['page'],
            'count' => $count,
            'limit' => $data['limit'],
            'affiliateId' => $data['affiliateId'],
            'isBeta' => $user['isBeta'] == '1',
        ];
    }

    public function getDashboardData($data)
    {
        $ticketConstants = config('constants.ticket');
        $limit = $data['limit'];
        $page = $data['page'];
        $type = $data['type'];
        $role = $data['role'];
        $status = $data['status'];
        $search = $data['search'];
        $transport = $data['transport'];
        if ($role == 'isf') {
            $transport = $ticketConstants['transport']['OCEAN'];
        }
        $agent_status = $data['agent_status'];
        $affiliateId = $data['affiliateId'];
        $user = auth()->user();
        $userRole = $this->userRoleRepo->findOne([['internalKey', $role]]);

        $includeDeleted = $data['search'] ? true : false;
        $where = '';

        if (!empty($search) && is_numeric($search) && strlen($search) <= 7) {
            $id = $this->ticketRepo->getTicketId($search);
            $where .= ' AND (ticket.`id`="' . $id . '"  OR ticket.`transactionnumber` like "%' . $search . '%" OR ticket.`affiliatereferencenumber` like "%' . $search . '%" OR ticket.`mBOL` like "%' . $search . '%" OR ticket.`hBOL` like "%' . $search . '%")';
        } elseif (!empty($search)) {
            $where .= ' AND (ticket.`vendor_carrier_ref` like "%' . $search . '%" OR ticket.`firstname` like "%' . $search . '%" OR ticket.`lastname` like "%' . $search . '%" OR ticket.`email` like "%' . $search . '%" OR ticket.`transactionnumber` like "%' . $search . '%" OR ticket.`busname` like "%' . $search . '%" OR u.`busname` like "%' . $search . '%" OR u.`tradename` like "%' . $search . '%" OR u.`email` like "%' . $search . '%" OR CONCAT(ticket.`lastname`," ",ticket.`firstname`) like "%' . $search . '%" OR sold_to.`busname` like "%' . $search . '%" OR sold_to.`firstname` like "%' . $search . '%" OR sold_to.`lastname` like "%' . $search . '%" OR sold_to.`email` like "%' . $search . '%" OR ticket.`affiliatereferencenumber` like "%' . $search . '%" OR ticket.`mBOL` like "%' . $search . '%" OR ticket.`hBOL` like "%' . $search . '%" OR ticket.`containernumber` like "%' . $search . '%")';
        }
        if (!empty($transport)) {
            $where .= ' AND ticket.`transport` = ' . $transport;
        }
        if (!empty($agent_status) && $agent_status > 0) {
            $where .= ' AND ticket.`agentStatusTypeId` = ' . $agent_status;
        }
        if ($affiliateId > 0) {
            $where .= ' AND ticket.`affiliateId` = ' . $affiliateId;
        }

        if (!$includeDeleted) {
            $where .= ' AND  ticket.`isDeleted` = 0';
        }
        // $ticketStatusList = $this->ticketRepo->getUsedTicketStatusList()->toArray();

        $dash = $this->userRepo->getDashboardData(
            $data['userId'],
            $userRole->id,
            (int) $data['roleBitmask'],
            $where,
            (int) ($page - 1) * $limit,
            (int) $limit,
        );
        return [
            'type' => $type,
            'role' => $role,
            'status' => $data['status'],
            'from' => $data['from'],
            'to' => $data['to'],
            'agent' => $data['agent'],
            'search' => $search,
            'transport' => $transport,
            'agent_status' => $agent_status,
            'page' => $page,
            'limit' => $limit,
            'ticketStatusList' => [],
            'affiliateId' => $affiliateId,
            'isBeta' => $user['isBeta'] == '1',
            'list' => $dash['list'],
            'agentStatusTypes' => $dash['agentStatusTypes'],
            'count' => $dash['count'],
            'affiliates' => $dash['affiliates'],
            'roles' => $dash['roles'],
            'agents' => $dash['agents']
        ];
    }
}
