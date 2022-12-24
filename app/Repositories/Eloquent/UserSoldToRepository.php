<?php

namespace App\Repositories\Eloquent;

use App\Models\UserSoldTo;

class UserSoldToRepository extends BaseRepository
{

    const MODEL_LABEL = 'User Sold To';

    public function __construct(
        UserSoldTo $model
    ) {
        parent::__construct($model);
    }

    public function findByTicket($ticket)
    {
        return $this->findOne([['userId', '=', $ticket['userid']], ['id', '=', $ticket['soldToId']]]);
    }
}
