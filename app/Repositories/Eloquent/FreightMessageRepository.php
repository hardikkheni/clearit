<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Models\FreightMessage;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FreightMessageRepository extends BaseRepository
{
    use HasTicketColumn;

    const MODEL_LABEL = 'Freight Message';

    public function __construct(
        FreightMessage $model
    ) {
        parent::__construct($model);
    }

    public function getMessageListByTicketId($ticketId)
    {
        $q = $this->findAndOrSort(['ticketId' => $ticketId], [['id', 'ASC']]);
        $q->select(['*', DB::raw("IF(`isMaster` = 1, (SELECT `firstname` FROM `user` WHERE `id` = `message_freight`.`ISFConsolidator_id`), (SELECT `isfcname` FROM `ISFConsolidator` WHERE `id` = `message_freight`.`ISFConsolidator_id`)) as name")]);
        return $q->get();
    }

    public function createChatMessage($guid, $ticketGuid, $message, $userGuid, $isMaster)
    {
        if (!$ticketGuid) {
            return true;
        }
        return $this->call('createChatMessage', [$guid, $ticketGuid, $message, $userGuid, $isMaster]);
        try {
        } catch (\Exception $e) {
            Log::alert(sprintf("[ALERT calling procedure createChatMessage]: ERROR:" . $e->getMessage()));
        }
    }
}
