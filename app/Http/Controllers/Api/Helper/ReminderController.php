<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\Reminder\EditReminderRequest;
use App\Http\Requests\Api\Helper\Reminder\InsertReminderRequest;
use App\Services\Helper\ReminderService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ReminderController extends BaseApiController
{
    private $reminderService;

    public function __construct(
        ReminderService $reminderService
    ) {
        $this->reminderService = $reminderService;
    }

    public function list($filter, Request $request)
    {
        return $this->respond($this->reminderService->getMyReminders($filter, $request->user()->id));
    }

    public function create(InsertReminderRequest $request)
    {
        $reminder = $this->reminderService->create($request->validated());
        return $this->respond($reminder, HttpStatuses::HTTP_CREATED, 'Reminder successfully created!.');
    }

    public function edit($id, EditReminderRequest $request)
    {
        try {
            $data = $request->validated();
            $data = $this->transformObject($data);
            $reminder = $this->reminderService->edit($id, $data);
            return $this->respond($reminder, HttpStatuses::HTTP_OK, 'Reminder successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Reminder not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function delete($id)
    {
        try {
            $reminder = $this->reminderService->delete($id);
            return $this->respond($reminder, HttpStatuses::HTTP_OK, 'Reminder successfully deleted!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Reminder not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function completeReminders(Request $request)
    {
        try {
            $reminder = $this->reminderService->completeReminders($request, $request->user()->id);
            return $this->respond($reminder, HttpStatuses::HTTP_OK, 'Reminder successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Reminder not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    protected function transformObject($data)
    {
        $data['ticketId'] = filter_var(@$data['ticketId'], FILTER_VALIDATE_INT);
        $data['assignedToUserId'] = filter_var(@$data['assignedToAgentId'], FILTER_VALIDATE_INT);
        unset($data['assignedToAgentId']);
        $data['dueOn'] = $data['dueOnDate'] . ' ' . $data['dueOnTime'];
        unset($data['dueOnDate']);
        unset($data['dueOnTime']);

        return $data;
    }
}
