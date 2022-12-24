<?php

namespace App\Repositories\Eloquent;

use App\Helpers\DataTable\HasDataTable;
use App\Helpers\Eloquent\Columns\HasGuidColumn;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{

    use HasGuidColumn, HasDataTable;

    const MODEL_LABEL = 'User';

    protected $affiliateRepo;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(
        User $model,
        AffiliateRepository $affiliateRepo
    ) {
        parent::__construct($model);
        $this->affiliateRepo = $affiliateRepo;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    public function getNameById($id)
    {
        $user = $this->findById($id);
        if ($user) {
            if (in_array($user->status, config('constants.user.COMPANY_USER_STATUSES'))) {
                if ($user->tradename) return $user->tradename;
                return $user->busname;
            } else {
                return $user->lastname . ', ' . $user->firstname;
            }
        }
        return 'N/A';
    }

    public function getUserByRoleBitMask($roleBitMask)
    {
        return $this->findAndOrSort([[DB::raw("(roleBitmask & $roleBitMask)"), $roleBitMask]])->get();
    }

    public function findByEmailOrLogin($needle)
    {
        return $this->model->where('email', $needle)->orWhere('login', $needle)->firstOrFail();
    }

    public function getUsedAgentList()
    {
        $aidSelect = DB::table('ticket')->select([DB::raw('DISTINCT agentId')])->whereRaw('agentId IS NOT NULL')->toSql();
        $paidSelect = DB::table('ticket')->select([DB::raw('DISTINCT processingAgentId')])->whereRaw('processingAgentId IS NOT NULL')->toSql();
        return $this->model->query()->select([
            'id',
            'guid',
            'firstname',
            'lastname',
        ])->agent()
            ->whereRaw("(id IN ($aidSelect) OR id IN ($paidSelect))")
            ->orderBy('firstname', 'ASC')->orderBy('lastname', 'ASC')->get();
    }

    public function getAgentList($where = [], $sorts = [], $includeDeleted = false)
    {
        $q = $this->findAndOrSort($where, $sorts)->agent();
        if (!$includeDeleted) {
            $q->withoutTrashed();
        }
        return $q->get();
    }

    /**
     * @param mixed  $userId
     * @param mixed  $userRoleId
     * @param mixed  $roleBitmask
     * @param mixed  $where
     * @param int  $page
     * @param int  $limit
     *
     * @return array
     */
    public function getDashboardData(
        $userId,
        $userRoleId,
        $roleBitmask,
        $where,
        $page,
        $limit
    ) {
        $data = $this->call('getDashboardData', [
            $userId,
            $userRoleId,
            $roleBitmask,
            $page,
            $limit,
            $where
        ]);
        $count = (int) ($data[2][0]->count ?? 0);
        return [
            'list' => $data[0] ?? [],
            'agentStatusTypes' => $data[1] ?? [],
            'count' => $count,
            'affiliates' => $data[3] ?? [],
            'roles' => $data[4] ?? [],
            'agents' => $data[5] ?? []
        ];
    }

    protected function search(Builder $query, $search, $extra)
    {
        $query->where('isVerified', true);
        if ($search) {
            $query->where(function (Builder $query)  use ($search) {
                $query->where($this->model->getTable() . '.id', '=', $search)
                    ->orWhere($this->model->getTable() . '.email', 'LIKE', "%$search%")
                    ->orWhere($this->model->getTable() . '.firstname', 'LIKE', "%$search%")
                    ->orWhere(DB::raw('concat("' . $this->model->getTable() . '.lastname"," ","' . $this->model->getTable() . '.firstname")'), 'LIKE', "%$search%")
                    ->orWhere(DB::raw('concat("' . $this->model->getTable() . '.firstname"," ","' . $this->model->getTable() . '.lastname")'), 'LIKE', "%$search%")
                    ->orWhere($this->model->getTable() . '.lastname', 'LIKE', "%$search%")
                    ->orWhere($this->model->getTable() . '.busname', 'LIKE', "%$search%")
                    ->orWhere($this->model->getTable() . '.tradename', 'LIKE', "%$search%");
            });
        }
    }

    protected function select(Builder $query, $select)
    {
        $query->select(
            $this->model->getTable() . ".*",
            DB::raw("getAdjustedDatetime (" . $this->model->getTable() . ".CreatedOn, " . $this->model->getTable() . ".id) AS CreatedAt"),
            DB::raw("COUNT(user_document.id) AS ispga"),
            DB::raw("COUNT(payment.id) AS iscc")
        );
        $query->leftJoin('user_document', function ($join) {
            $join->on($this->model->getTable() . ".id", '=', 'user_document.userId')
                ->on('user_document.documentType', '=', DB::raw(2));
        });
        $query->leftJoin('payment', $this->model->getTable() . ".id", '=', 'payment.userId');
        $query->groupBy($this->model->getTable() . '.id');
    }

    public function unverifiedWithPoaDataTable($data)
    {
        $affiliateConstants = config('constants.affiliate');
        return $this->useDataTable($data, function (Builder $query, $search, $extra) use ($affiliateConstants) {
            $query->from('user as u');
            $query->leftJoin('affiliate as af', function (JoinClause $join) use ($affiliateConstants) {
                $join->on('af.id', 'u.affiliateId')->where('af.affiliateCode', $affiliateConstants['FREIGHTOSCODE']);
            });
            $query->when($search, function (Builder $q) use ($search) {
                $q->orWhere('u.id', $search);
                $q->orWhere('u.email', "%$search%");
                $q->orWhere('u.firstname', "%$search%");
                $q->orWhere('u.lastname', "%$search%");
                $q->orWhere('u.busname', "%$search%");
                $q->orWhere('u.tradename', "%$search%");
                $q->orWhereRaw("concat(u.lastname, ' ', u.firstname) LIKE '%$search%'");
                $q->orWhereRaw("concat(u.firstname, ' ', u.lastname) LIKE '%$search%'");
            });
            $query->where('isCustomer', true);
            if ($extra['affiliateId']) {
                $query->where('u.affiliateId', $extra['affiliateId']);
            }
            $query->where('u.isVerified', false);
            if (!($extra['all'] == 1)) {
                $query->where('u.isSigned', true);
                $query->whereNotNull('u.pdfSignUrl');
                $query->where('u.pdfSignUrl', "<>", '');
            }
            $query->where('u.isDeleted', false);
        }, function (Builder $query) use ($affiliateConstants, $data) {
            $select = ['u.*', 'af.companyname as affiliate_company_name'];
            $freightosAffiliate = $this->affiliateRepo->affiliateExistByCode($affiliateConstants['FREIGHTOSCODE']);
            if ($freightosAffiliate) {
                $select[] = DB::raw("IF(u.affiliateId={$freightosAffiliate->id}, 1, 0) as is_freightos_customer");
            }
            // if (!($data['extra']['all'] == 1)) {
            //     $select[] = DB::raw("EXISTS (SELECT 1 FROM `user_sign` as us WHERE us.userId = u.id AND  us.isSigned = 0 ) as unsignedsignerexists");
            // }
            $query->select($select)->orderBy('u.isSigned', 'DESC')->orderBy('u.pdfSignUrl', 'DESC');
        });
    }
}
