<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasTicketColumn;
use App\Models\FreightosCharge;
use App\Repositories\Eloquent\BaseRepository;
use Illuminate\Support\Facades\DB;

class FreightosChargeRepository extends BaseRepository
{
    use HasTicketColumn;

    const MODEL_LABEL = 'Freight Charge';

    public function __construct(FreightosCharge $model)
    {
        parent::__construct($model);
    }

    public function getFcDateTimeByTicketId($ticketId)
    {
        return $this->findAndOrSort(['ticketId' => $ticketId])->select([
            DB::raw('DATE_FORMAT(MAX(sentOn), \'%M %D, %Y\') as max_date'),
            DB::raw("DATE_FORMAT(getAdjustedDatetime(MAX(sentOn), $ticketId), '%l:%i%p') as createdon_format_time")
        ])->first();
    }
}
