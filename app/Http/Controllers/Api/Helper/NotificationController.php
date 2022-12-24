<?php

namespace App\Http\Controllers\Api\Helper;

use App\Helpers\Http\HttpStatuses;
use App\Http\Controllers\Api\BaseApiController;
use App\Http\Requests\Api\Helper\Notification\MarkViewedNotificationRequest;
use App\Services\Helper\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    protected $notiService;

    public function __construct(
        NotificationService $notiService
    ) {
        $this->notiService = $notiService;
    }

    public function list(Request $request)
    {
        $data = $request->all();
        $isAffiliate = filter_var(@$data['is_affiliate'], FILTER_VALIDATE_BOOLEAN);
        return $this->respond($this->notiService->list($isAffiliate));
    }

    public function markViewed(MarkViewedNotificationRequest $request)
    {
        $data = $this->notiService->markViewedNotifications($request->validated());
        return $this->respond($data, HttpStatuses::HTTP_OK, "Notifications marked viewed successfully!.");
    }
}
