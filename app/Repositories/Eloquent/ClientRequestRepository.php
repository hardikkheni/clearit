<?php

namespace App\Repositories\Eloquent;

use App\Models\ClientRequest;
use App\Repositories\Eloquent\BaseRepository;

class ClientRequestRepository extends BaseRepository
{

    const MODEL_LABEL = 'Client Request';

    public function __construct(
        ClientRequest $model
    ) {
        parent::__construct($model);
    }

    public function getClientRequestItems($userRoleId, $modeOfTransport)
    {
        $data = $this->call('getClientRequestItems', [$userRoleId, $modeOfTransport]);
        return $data[0] ?? [];
    }

    public function loadRepliesTab($agentId)
    {
        $data = $this->call('loadRepliesTab', [$agentId]);
        return $data[0] ?? [];
    }

    public function markReadClientRequestNotification($id)
    {
        $this->call('markReadClientRequestNotification', [$id]);
    }

    public function getDailyClientRequestEmails($id)
    {
        $data = $this->call('getDailyClientRequestEmails', [$id]);
        return $data[0] ?? [];
    }
}
