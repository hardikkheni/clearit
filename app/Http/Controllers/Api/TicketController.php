<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Http\HttpStatuses;
use App\Http\Requests\Api\Ticket\AddAffiliateReferanceRequest;
use App\Http\Requests\Api\Ticket\AddBillingRequest;
use App\Http\Requests\Api\Ticket\AddCarrierDetailsRequest;
use App\Http\Requests\Api\Ticket\AddPgaRequest;
use App\Http\Requests\Api\Ticket\AddTicketEtaRequest;
use App\Http\Requests\Api\Ticket\AttachUserHtsRequest;
use App\Http\Requests\Api\Ticket\DeleteTicketRequest;
use App\Http\Requests\Api\Ticket\NotifyTariffCodeRequest;
use App\Http\Requests\Api\Ticket\PutToDoTicketItemRequest;
use App\Http\Requests\Api\Ticket\ReOrderTicketStatusRequest;
use App\Http\Requests\Api\Ticket\ReOrderToDoTicketItemRequest;
use App\Http\Requests\Api\Ticket\UpsertTicketStatusRequest;
use App\Http\Requests\Api\Ticket\UpsertToDoTicketItemRequest;
use App\Services\TicketService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TicketController extends BaseApiController
{
    protected $ticketService;

    public function __construct(
        TicketService $ticketService
    ) {
        $this->ticketService = $ticketService;
    }

    public function getTicketStatusDependencies(Request $request)
    {
        return $this->respond($this->ticketService->getTicketStatusDependencies($request->all()));
    }

    public function putTicketStatusDependencies(PutToDoTicketItemRequest $request)
    {
        try {
            $data = $this->ticketService->putTicketStatusDependencies($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "ToDoTicketItem successfully updated!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function getTodoTicketItemList(Request $request)
    {
        return $this->respond($this->ticketService->getTodoTicketItemList($request->all()));
    }

    public function upsertTodoTicketItem(UpsertToDoTicketItemRequest $request)
    {
        try {
            $data = $this->ticketService->upsertTodoTicketItem($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "To-Do Ticket Item successfully saved!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function deleteTodoTicketItem($id)
    {
        try {
            $data = $this->ticketService->deleteTodoTicketItem($id);
            return $this->respond($data, HttpStatuses::HTTP_OK, "To-Do Ticket Item successfully saved!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function reOrderTodoTicketItemList(ReOrderToDoTicketItemRequest $request)
    {
        try {
            $data = $this->ticketService->reOrderTodoTicketItemList($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "To-Do Ticket Item list successfully re-ordered!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function freightosBillingData(Request $request)
    {
        $data = $this->ticketService->freightosBillingData($request->all());
        return $this->respond($data);
    }

    public function getTicket($role, $guid, Request $request)
    {
        try {
            $agentNotificationId = $request->get('agent_notification_id');
            if ($role == 'master') {
                return $this->respond($this->ticketService->getTicketMaster($guid, $agentNotificationId));
            } else {
                return $this->respond($this->ticketService->getTicketRole($role, $guid, $agentNotificationId));
            }
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function getFreightInvoiceItemList($id)
    {
        try {
            return $this->respond($this->ticketService->getFreightInvoiceItemList($id));
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function deleteFreightInvoiceItem($invoiceItemId)
    {
        try {
            return $this->respond($this->ticketService->deleteFreightInvoiceItem($invoiceItemId));
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function getFcDatetime($id)
    {
        try {
            return $this->respond($this->ticketService->getFcDatetime($id));
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function getFcInvoiceDatetime($id)
    {
        try {
            return $this->respond($this->ticketService->getFcInvoiceDatetime($id));
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function getTicketStatusList(Request $request)
    {
        return $this->respond($this->ticketService->getTicketStatusList($request->all()));
    }

    public function deleteTicketStatus($id)
    {
        try {
            $data = $this->ticketService->deleteTicketStatus($id);
            return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket Status successfully saved!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }


    public function upsertTicketStatus(UpsertTicketStatusRequest $request)
    {
        try {
            $data = $this->ticketService->upsertTicketStatus($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket Status successfully saved!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function reOrderTicketStatusList(ReOrderTicketStatusRequest $request)
    {
        try {
            $data = $this->ticketService->reOrderTicketStatusList($request->validated());
            return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket Status list successfully re-ordered!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function dailyReport(Request $request)
    {
        switch ($request->get('report')) {
            case 'isverified':
                return $this->respond($this->ticketService->getISFNotFiled(1, $request->get('page') ? $request->get('page') : 1));
            case 'notverified':
                return $this->respond($this->ticketService->getISFNotFiled(0, $request->get('page') ? $request->get('page') : 1));
            case 'iskeyed':
                return $this->respond($this->ticketService->getArrivalNoticeTickets(1, $request->get('page') ? $request->get('page') : 1));
            case 'notkeyed':
                return $this->respond($this->ticketService->getArrivalNoticeTickets(0, $request->get('page') ? $request->get('page') : 1));
            case 'prekey_billing':
                return $this->respond($this->ticketService->getPrekeyBillingTickets($request->get('page') ? $request->get('page') : 1));
            case 'release_team_tickets':
                return $this->respond($this->ticketService->getReleaseTeamTickets($request->get('page') ? $request->get('page') : 1));
            case 'isf_ticket':
                return $this->respond($this->ticketService->getIsfTickets($request->get('page') ? $request->get('page') : 1));
        }
    }

    public function updateRequireBrokerReview(Request $request, $id)
    {
        try {
            $data = $this->ticketService->updateRequireBrokerReview($id, $request->requires_broker_review);
            return $this->respond($data, HttpStatuses::HTTP_OK, "Ticket requires broker review successfully updated!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function delete(DeleteTicketRequest $request, $id)
    {
        try {
            $ticket = $this->ticketService->delete($id, $request->validated());
            return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket $id successfully deleted!.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Ticket $id not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function patch(Request $request, $id)
    {
        try {
            $data = $request->all();
            $ticket = $this->ticketService->patch($id, $data);
            return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket $id successfully updated!.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Ticket $id not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function updateTicketStatus(Request $request, $id)
    {
        try {
            $ticket = $this->ticketService->updateTicketStatus($id, $request->statusId);
            return $this->respond($ticket, HttpStatuses::HTTP_OK, "Status successfully updated for ticket $id!.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Ticket $id not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function updateAgent(Request $request, $id)
    {
        try {
            $ticket = $this->ticketService->updateAgent($id, $request->agentid);
            return $this->respond($ticket, HttpStatuses::HTTP_OK, "Agent successfully updated for ticket $id !.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Ticket $id not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function updateProcessingAgent(Request $request, $id)
    {
        try {
            $ticket = $this->ticketService->updateProcessingAgent($id, $request->processingAgentId);
            return $this->respond($ticket, HttpStatuses::HTTP_OK, "Processing agent successfully updated for ticket $id !.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Ticket $id not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function removeAffiliateReferance($id, Request $request)
    {
        try {
            $ticket = $this->ticketService->removeAffiliateReferance($id, $request->ip());
            return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket affiliate referance successfully removed!.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Ticket $id not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function addAffiliateReferance($id, AddAffiliateReferanceRequest $request)
    {
        try {
            $ticket = $this->ticketService->addAffiliateReferance($id, $request->validated());
            return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket affiliate referance successfully added!.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Ticket $id not found!.");
        } catch (\Exception $e) {
            if ($e instanceof ValidationException) throw $e;
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function addCarrierDetails($id, AddCarrierDetailsRequest $request)
    {
        $ticket = $this->ticketService->addCarrierDetails($id, $request->validated());
        return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket carrier details successfully added!.");
    }

    public function addEta($id, AddTicketEtaRequest $request)
    {
        $ticket = $this->ticketService->addEta($id, $request->validated());
        return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket carrier details successfully added!.");
    }

    public function addBilling($id, AddBillingRequest $request)
    {
        $data = $request->validated();
        $data['doNotCharge'] = filter_var(@$data['doNotCharge'], FILTER_VALIDATE_BOOLEAN);
        $ticket = $this->ticketService->addBilling($id, $data);
        return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket billing details successfully added!.");
    }

    public function markAsPaid($id, $payId)
    {
        $ticket = $this->ticketService->markAsPaid($id, $payId);
        return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket mark as paid successfully!.");
    }

    public function removePayment($id, $payId)
    {
        $ticket = $this->ticketService->removePayment($id, $payId);
        return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket payment successfully removed!.");
    }

    public function addPgaRequest($id, AddPgaRequest $request)
    {
        $ticket = $this->ticketService->addPgaRequest($id, $request->validated());
        return $this->respond($ticket, HttpStatuses::HTTP_CREATED, "Ticket pga request successfully created!.");
    }

    public function attachUserHts($id, AttachUserHtsRequest $request)
    {
        $ticket = $this->ticketService->attachUserHts($id, $request->validated());
        return $this->respond($ticket, HttpStatuses::HTTP_CREATED, "User Hts is attached to ticket successfully!.");
    }

    public function sendNotifyTariffCodeEmail($id, NotifyTariffCodeRequest $request)
    {
        $ticket = $this->ticketService->sendNotifyTariffCodeEmail($id, $request->validated());
        return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket tariff code successfully notified!.");
    }

    public function updateNotifyTariffCode($id, Request $request)
    {
        $ticket = $this->ticketService->updateNotifyTariffCode($id, $request->all());
        return $this->respond($ticket, HttpStatuses::HTTP_OK, "Ticket tariff code successfully updated!.");
    }
}
