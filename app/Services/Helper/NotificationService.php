<?php

namespace App\Services\Helper;

use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\TicketRepository;
use App\Repositories\Eloquent\UserRepository;

class NotificationService
{
    protected $notiRepo;
    protected $userRepo;
    protected $ticketRepo;

    public function __construct(
        NotificationRepository $notiRepo,
        UserRepository $userRepo,
        TicketRepository $ticketRepo
    ) {
        $this->notiRepo = $notiRepo;
        $this->userRepo = $userRepo;
        $this->ticketRepo = $ticketRepo;
    }

    public function markViewedNotifications($data)
    {
        if ($data['all']) {
            $this->notiRepo->markReadAllAgentNotifications(auth()->user()->id, $data['is_affiliate']);
        } else {
            foreach ($data['ids'] as $id) {
                $this->notiRepo->markReadAgentNotification($id);
            }
        }
    }

    public function list($isAffiliate)
    {
        if ($isAffiliate) {
            $count = $this->notiRepo->getAgentAffiliateNotificationCount();
            $list = $this->notiRepo->getAgentAffiliateNotificationList();
        } else {
            $count = $this->notiRepo->getNotificationCount();
            $list = $this->notiRepo->getNotificationList();
        }
        $list = $this->listPostPorcess($list);
        return compact('count', 'list');
    }

    public function listPostPorcess($notifications)
    {
        $notificationConstants = config('constants.notification');
        $notifies = [];
        $userConstants = config('constants.user');
        foreach ($notifications as $notification) {
            $user = $this->userRepo->findById($notification['userId']);
            if ($user['status'] == $userConstants['USER_STATUS_COMMERCAIL']) {
                if ($user['busname']) {
                    $name = $user['busname'];
                } else {
                    $name = $user['tradename'];
                }
            } else {
                $name = $user['firstname'] . ' ' . $user['lastname'];
            }
            $notification['user_data'] = $user;
            $notification['title'] = $name;

            if ($notification['ticketId']) {
                $notification['ticket_data'] = $this->ticketRepo->findById($notification['ticketId']);
            }
            $message = '';
            $agentNotificationId = !$notification['notification'] ? 0 : $notification['agent_notification_id'];
            $affiliateNotificationId = !$notification['affiliate_notification_id'] ? 0 : $notification['affiliate_notification_id'];
            switch ($notification['type']) {
                case 'upload':
                    $notification['title'] .= ' #' . TicketRepository::getTicketNumberFromId($notification['ticketId']);
                    $message = 'Document upload - ' . $notification['description'];
                    break;
                case 'upload_trade':
                    $message = 'Trade certificate uploaded - ' . $notification['description'];
                    break;
                case 'upload_trade':
                    $message = 'Trade certificate uploaded - ' . $notification['description'];
                    break;
                case 'payment_profile':
                    $notification['title'] .= ' #' . TicketRepository::getTicketNumberFromId($notification['ticketId']);
                    $message = 'Payment of $' . $notification['description'] . ' processed by profile';
                    break;
                case 'profile':
                    $message = 'Payment profile is created';
                    break;
                case 'payment_card':
                    $notification['title'] .= ' #' . TicketRepository::getTicketNumberFromId($notification['ticketId']);
                    $message = 'Payment of $' . $notification['description'] . ' processed by card';
                    break;
                case 'assign':
                case 'pga_sign':
                    $notification['title'] .= ' #' . TicketRepository::getTicketNumberFromId($notification['ticketId']);
                    $message = $notification['description'];
                    break;
                case 'annual_request':
                    $message = $notification['description'];
                    break;
                case 'reminder':
                    $notification['title'] = 'Reminder for ticket #' . TicketRepository::getTicketNumberFromId($notification['ticketId']);
                    $message = $notification['description'];
                    break;
                case 'customer_request':
                    if (@$notification['ticketId']) {
                        $notification['title'] = 'Customer Request for ticket #' . TicketRepository::getTicketNumberFromId($notification['ticketId']);
                    } else {
                        $notification['title'] = 'Customer Request for ' . $name;
                    }
                    $message = $notification['description'];
                    break;
                case $notificationConstants['type']['CUSTOMER_VERIFIED']:
                    $notification['title'] .= ' Customer verified';
                    $message = $notification['description'];
                    break;
                case $notificationConstants['type']['AFFILIATE_DOCUMENT_UPLOAD']:
                    $message = 'Document upload - ' . $notification['description'];
                    break;
            }
            $notification['message'] = $message;
            $notifies[] = $notification;
        }
        return $notifies;
    }
}
