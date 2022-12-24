<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\Affiliate\EditAffiliateRequest;
use App\Http\Requests\Api\Helper\Affiliate\InsertAffiliateRequest;
use App\Http\Requests\Api\Helper\Affiliate\RegisterAffiliateRequest;
use App\Services\Helper\AffiliateService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AffiliateController extends BaseApiController
{
    private $affiliateService;

    public function __construct(
        AffiliateService $affiliateService
    ) {
        $this->affiliateService = $affiliateService;
    }

    public function list(Request $request)
    {
        return $this->respond($this->affiliateService->dataTable($request->all()));
    }

    public function allAffiliates()
    {
        return $this->respond($this->affiliateService->allAffiliates());
    }

    public function create(InsertAffiliateRequest $request)
    {
        $data = $request->validated();
        $data = $this->tranformObject($data);
        return $this->respond($this->affiliateService->create($data), HttpStatuses::HTTP_CREATED, 'Affiliate successfully created!.');
    }

    public function get($id)
    {
        try {
            $agent = $this->affiliateService->findOrFail($id);
            return $this->respond($agent);
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Affiliate not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, "Something went wrong!.");
        }
    }

    public function edit($id, EditAffiliateRequest $request)
    {
        try {
            $data = $request->validated();
            $data = $this->tranformObject($data);
            $data['remove_logo'] = filter_var(@$data['remove_logo'], FILTER_VALIDATE_BOOLEAN);
            $affiliate = $this->affiliateService->edit($id, $data);
            return $this->respond($affiliate, HttpStatuses::HTTP_OK, 'Affiliate successfully updated!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Affiliate not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    protected function tranformObject($data)
    {
        $data['expressEnabled'] = filter_var(@$data['expressEnabled'], FILTER_VALIDATE_BOOLEAN);
        $data['isPaymentProfileRequired'] = filter_var(@$data['isPaymentProfileRequired'], FILTER_VALIDATE_BOOLEAN);
        $data['isCreditAccount'] = filter_var(@$data['isCreditAccount'], FILTER_VALIDATE_BOOLEAN);
        $data['disableChatEmails'] = filter_var(@$data['disableChatEmails'], FILTER_VALIDATE_BOOLEAN);
        $data['isActive'] = filter_var(@$data['isActive'], FILTER_VALIDATE_BOOLEAN);
        return $data;
    }

    public function delete($id)
    {
        try {
            $affiliate = $this->affiliateService->delete($id,);
            return $this->respond($affiliate, HttpStatuses::HTTP_OK, 'Affiliate successfully deleted!.');
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Affiliate not found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }

    public function registerAffiliate(RegisterAffiliateRequest $request)
    {
        $data = $request->validated();
        try {
            $registeredAffiliate = $this->affiliateService->registerAffiliate($data);
            if(@$registeredAffiliate['ticket_id'])
                return $this->respond(null, HttpStatuses::HTTP_OK, "There is already a user account for " . $data['email'] . ". Ticket #" . $registeredAffiliate['ticket_id'] . " has been created.");
            else
                return $this->respond(null, HttpStatuses::HTTP_OK, "An email has been sent to the customer, you will be notified once they have completed their registration.");
        } catch (ModelNotFoundException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, "Affiliate not found!.");
        } catch (ValidationException $e) {
            return $this->respond(null, HttpStatuses::HTTP_NOT_FOUND, $e->errors() ?? "Affiliate data found!.");
        } catch (\Exception $e) {
            return $this->respond(null, HttpStatuses::HTTP_BAD_REQUEST, $e->getMessage() ?? "Something went wrong!.");
        }
    }
}
