<?php

namespace App\Repositories\Eloquent;

use App\Models\AgentMessage;

class AgentMessageRepository extends BaseRepository
{

    const MODEL_LABEL = 'Affiliate Message';

    public function __construct(
        AgentMessage $model
    ) {
        parent::__construct($model);
    }

    public function findByTicketId($ticketId)
    {
        return $this->model->from('agent_message as am')
            ->leftJoin('message as m', 'am.messageId', 'm.id')
            ->where('m.ticketId', $ticketId)
            ->whereNull('am.readOn')->orderBy('am.messageId', 'DESC')
            ->get();
    }
}
