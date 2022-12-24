<?php

namespace App\Repositories\Eloquent;

use App\Helpers\DataTable\HasDataTable;
use App\Models\Affiliate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class AffiliateRepository extends BaseRepository
{

    use HasDataTable;

    const MODEL_LABEL = 'Affiliate';

    public function __construct(
        Affiliate $model
    ) {
        parent::__construct($model);
    }

    /**
     * Check is affiliate code is freightos
     * @param Affiliate|int $affiliateOrId
     * 
     * @return boolean 
     */
    public static function isFreightosTicket($affiliateOrId)
    {
        if (!is_object($affiliateOrId)) {
            $affiliateOrId = App::make(self::class)->findById($affiliateOrId);
        }
        return $affiliateOrId && $affiliateOrId['affiliateCode'] == config('constants.affiliate.FREIGHTOSCODE');
    }

    public function affiliateExistByCode($code)
    {
        return $this->model->where('affiliateCode', $code)->first();
    }

    protected function search(Builder $query, $search, $extra)
    {
        $query->where('isDeleted', false);
        if ($search) {
            $query->where(function (Builder $query)  use ($search) {
                $query->orWhere('companyname', 'LIKE', "%$search%");
                $query->orWhere('contactfirstname', 'LIKE', "%$search%");
                $query->orWhere('contactfirstname', 'LIKE', "%$search%");
                $query->orWhere('phone', 'LIKE', "%$search%");
                $query->orWhere('notificationEmail', 'LIKE', "%$search%");
                $query->orWhere('website', 'LIKE', "%$search%");
                $query->orWhere('affiliateCode', 'LIKE', "%$search%");
                // $query->orWhere('isCreditAccount', 'LIKE', "%$search%");
                $query->orWhere('createdByUserId', 'LIKE', "%$search%");
            });
        }

        if ($extra && $extra['isActive']) {
            $query->where('isActive', true);
        }
    }

    public function edit($id, $data)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        $affiliate = $this->findOrFail($id);
        $affiliate->fill(['isDeleted' => true])->save();
        return $affiliate;
    }

    public function findOrFail($id)
    {
        return $this->model->where(['isDeleted' => false, 'id' => $id])->firstOrFail();
    }

    public function getAffiliateByCode($code, $includeInactive = false, $includeDeleted = false)
    {
        $where = [['affiliatecode', $code]];
        if (!$includeInactive) {
            array_push($where, ['isactive', 1]);
        }
        if (!$includeDeleted) {
            array_push($where, ['isdeleted', 0]);
        }
        return $this->find($where);
    }

    public function getAffiliateList($search = null)
    {
        $query = $this->model->query()->select(['*', DB::raw('(select count(1) from user where user.`affiliateid` = affiliate.`id`) as referrals')]);
        if ($search) {
            $query->whereRaw("`companyname` like \"%$search%\" OR `contactfirstname` like \"%$search%\" OR concat(`contactlastname`,\" \",`contactfirstname`) like \"%$search%\" OR concat(`contactfirstname`,\" \",`contactlastname`) like \"%$search%\" OR `contactlastname` like \"%$search%\" OR `website` like \"%$search%\" OR `notificationemail` like \"%$search%\"");
        }
        $query->whereRaw('`isActive`=1');
        $query->whereRaw('`isDeleted`=0');
        return $query->get();
    }

    public function allAffiliates()
    {
        return $this->model->active()->orderBy('companyname', 'ASC')->get();
    }
}
