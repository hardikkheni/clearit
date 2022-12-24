<?php

namespace App\Repositories\Eloquent;

use App\Models\Notification;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\FuncCall;

class NotificationRepository extends BaseRepository
{

    const MODEL_LABEL = 'Notification';

    public function __construct(
        Notification $model
    ) {
        parent::__construct($model);
    }

    public function markReadAgentNotification($id)
    {
        $this->call('markReadAgentNotification', [$id]);
    }

    public function markReadTicketUploadAgentNotifications($ticketId)
    {
        $this->call('markReadTicketUploadAgentNotifications', [$ticketId]);
    }

    public function markReadAllAgentNotifications($agentId, $isAffiliate = 0)
    {
        $this->call('markReadAllAgentNotifications', [$agentId, $isAffiliate]);
    }

    public function getTicketNotifications($ticketId)
    {
        return $this->find(['ticketId' => $ticketId]);
    }

    public function getAgentAffiliateNotificationList()
    {
        $notificationConstants = config('constants.notification');
        $affiliateConstants = config('constants.affiliate');
        $userId = auth()->user()->id;
        $query = $this->model->from("notification as n");
        $query->leftJoin("agent_notification as an", "an.notificationid", "n.id");
        $query->leftJoin("ticket as t", "t.id", "n.ticketId");
        $query->leftJoin("affiliate as af", function (JoinClause $q) use ($affiliateConstants) {
            $q->on('af.id', 't.affiliateId')->where('af.affiliateCode', $affiliateConstants['FREIGHTOSCODE']);
        });
        $query->where('an.userid', $userId);
        $query->whereNull('an.readon');
        $query->whereIn('n.type', $notificationConstants['affiliateNotificationTypes']);
        $query->orderBy('n.createdOn', 'DESC');
        return $query->select(['n.*', 'an.id as agent_notification_id', 't.affiliateId as agent_affiliate_id', 'af.companyname as affiliate_company_name'])->limit(40)->get();
    }

    public function getAgentAffiliateNotificationCount()
    {
        $notificationConstants = config('constants.notification');
        $userId = auth()->user()->id;
        $query = $this->model->from("notification as n");
        $query->leftJoin("agent_notification as an", "an.notificationid", "n.id");
        $query->where('an.userid', $userId);
        $query->whereNull('an.readon');
        $query->whereIn('n.type', $notificationConstants['affiliateNotificationTypes']);
        return $query->count();
    }

    public function getNotificationCount()
    {
        $notificationConstants = config('constants.notification');
        $userId = auth()->user()->id;
        $query = $this->model->from("notification as n");
        $query->leftJoin("agent_notification as an", "an.notificationid", "n.id");
        $query->where('an.userId', $userId);
        $query->whereNull('an.readon');
        $query->whereNotIn('n.type', $notificationConstants['affiliateNotificationTypes']);
        return $query->count();
    }

    public function getNotificationList()
    {
        $notificationConstants = config('constants.notification');
        $userId = auth()->user()->id;
        $query = $this->model->from("notification as n");
        $query->leftJoin("agent_notification as an", "an.notificationid", "n.id");
        $query->where('an.userId', $userId);
        $query->whereNull('an.readon');
        $query->whereNotIn('n.type', $notificationConstants['affiliateNotificationTypes']);
        $query->orderBy('n.createdOn', 'DESC');
        return $query->limit(40)->get();
    }

    public function sendNotification($notification, $affiliateId = 0, $timeLineOnly = 0)
    {
        $noti = $this->create($notification);
        if ($timeLineOnly == 0) {
            $this->sendAgentNotification($noti['id']);
            if ($affiliateId) {
                $this->sendAffiliateNotification($noti['id'], $affiliateId);
            }
        }
    }

    public function sendAgentNotification($id)
    {
        if (!$id) return true;
        try {
            $this->call('createAgentNotifications', [$id]);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
        }
        return true;
    }

    public function sendAffiliateNotification($id, $affiliateId)
    {
        if (!$id || !$affiliateId) return true;
        try {
            $this->call('createAffiliateNotifications', [$id, $affiliateId]);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
        }
        return true;
    }

    public function sendReminderNotification($reminderId)
    {
        if (!$reminderId) return true;
        try {
            $this->call('createReminderNotificationStoredProcedure', [$reminderId]);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
        }
        return true;
    }

    public function sendClientRequestNotification($clientRequestId)
    {
        if (!$clientRequestId) return true;
        try {
            $this->call('createClientRequestNotificationStoredProcedure', [$clientRequestId]);
        } catch (\Exception $e) {
            Log::alert($e->getMessage());
        }
        return true;
    }
}
