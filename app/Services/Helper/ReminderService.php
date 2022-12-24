<?php

namespace App\Services\Helper;

use App\Models\Affiliate;
use App\Repositories\Eloquent\ReminderRepository;
use App\Repositories\Eloquent\AgentRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\TicketRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ReminderService
{
    protected $reminderRepo;
    protected $agentRepo;
    protected $ticketRepo;
    protected $notiRepo;

    public function __construct(
        ReminderRepository $reminderRepo,
        AgentRepository $agentRepo,
        TicketRepository $ticketRepo,
        NotificationRepository $notiRepo
    ) {
        $this->reminderRepo = $reminderRepo;
        $this->agentRepo = $agentRepo;
        $this->ticketRepo = $ticketRepo;
        $this->notiRepo = $notiRepo;
    }

    public function create($data)
    {
        $this->ticketRepo->findOrFail($data['ticketId']);
        $data['dueOnTime'] ??= '00:00:00';
        $dueOn = new Carbon("{$data['dueOnDate']} {$data['dueOnTime']}");
        $reminder = $this->reminderRepo->create([
            'ticketId' => $data['ticketId'],
            'assignedToUserId' => $data['assignedToUserId'],
            'message' => $data['message'],
            'dueOn' => $dueOn->format('Y-m-d H:i:s'),
        ]);
        if ($dueOn->format('Y-m-d') == (new Carbon)->format('Y-m-d')) {
            $this->notiRepo->sendReminderNotification($reminder['id']);
        }
        return $reminder;
    }

    public function edit($id, $data)
    {
        $this->reminderRepo->findOrFail($id);

        return $this->reminderRepo->edit($id, $data);
    }

    public function delete($id)
    {
        return $this->reminderRepo->delete($id);
    }

    public function completeReminders($data, $userId)
    {
        $ids = array();
        foreach ($data['id'] as $value) {
            $ids[] = $value;
        }
        return $this->reminderRepo->edit($ids, array('completedon' => date('Y-m-d h:i:s'), 'completedby' => $userId));
    }

    public function getMyReminders($filter, $data)
    {
        $allReminders = $this->reminderRepo->getMyReminders($data);
        if ($filter == 'all')
            return array('list' => $allReminders, 'agents' => $this->agentRepo->allInternalAgents());

        $reminders = array();
        foreach ($allReminders as $reminder) {
            if ($filter == 'my' && $reminder->assignedToUserId == $data)
                $reminders[] = $reminder;
            else if ($filter == 'others' && $reminder->assignedToUserId != $data)
                $reminders[] = $reminder;
        }
        return array('list' => $reminders, 'agents' => $this->agentRepo->allInternalAgents());
    }
}
