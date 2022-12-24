<?php

namespace App\Repositories\Eloquent;

use App\Models\UserHts;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserHtsRepository extends BaseRepository
{

    const MODEL_LABEL = 'User hts';

    public function __construct(
        UserHts $model
    ) {
        parent::__construct($model);
    }

    public function getAllHtsListByUserId($userId)
    {
        return $this->find(['userId' => $userId]);
    }

    public function getTicketHtsList($userId, $where = [], $sorts = [])
    {
        $where[] = ['uh.userId', +$userId];
        return $this->findAndOrSort($where, $sorts)->from('user_hts as uh')
            ->join('ticketUserHTS as tuh', 'tuh.userHtsId', 'uh.id')
            ->leftJoin('hts_code_xml as hcx', 'hcx.TariffNum', 'uh.code')
            ->leftJoin('hts_code_long_descriptions as hcd', function (JoinClause $join) {
                $join->on(DB::raw("REPLACE(uh.code, '.', '')"), DB::raw("REPLACE(hcd.htsCode, '.', '')"));
            })
            ->select(['uh.*', DB::raw('tuh.id as tuhId'), 'tuh.ticketId', 'tuh.isVerified', 'tuh.deletedOn', 'hcx.TariffNum', 'hcx.BasicDutyRateString', 'hcx.USTR301', 'hcx.PGACodes', 'hcd.agentDescription', 'hcd.mergedDescription'])->get();
    }

    public function create($data)
    {
        $data['createdBy'] = auth()->user()->id;
        $data['guid'] = Str::upper(Str::uuid());
        return parent::create($data);
    }
}
