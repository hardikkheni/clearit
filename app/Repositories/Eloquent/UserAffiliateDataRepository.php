<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasUserColumn;
use App\Models\UserAffiliateData;

class UserAffiliateDataRepository extends BaseRepository
{

    use HasUserColumn;

    const MODEL_LABEL = 'Affiliate Data';

    public function __construct(
        UserAffiliateData $model
    )
    {
        parent::__construct($model);
    }

    public function getAffiliateDataByUser($affiliateReference, $userId)
    {
        $where = [['affiliateReference', $affiliateReference],['userId', $userId]];
        return $this->model->where($where)->first();
    }

    public function getUnfinishedAffiliateDataList($userId)
    {
        return $this->find(['userId' => $userId, 'ticketId' => null]);
    }
}
