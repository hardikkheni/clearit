<?php

namespace App\Repositories\Eloquent;

use App\Helpers\DataTable\HasDataTable;
use App\Models\UserApi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ApiUserRepository extends BaseRepository
{
    use HasDataTable;

    const MODEL_LABEL = 'Api User';

    protected $agentPermission;

    public function __construct(UserApi $model)
    {
        parent::__construct($model);
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }


    public function create(array $data)
    {
        $data['password'] = md5($data['password']);
        $data['guid'] = Str::upper(Str::uuid());
        return parent::create($data);;
    }

    public function edit($id, $data)
    {
        if (@$data['password']) {
            $data['password'] = md5($data['password']);
        }
        return $this->update([['id', $id]], $data);
    }

    protected function search(Builder $query, $search, $extra)
    {

        if ($search) {
            $query->where(function (Builder $query)  use ($search) {
                $query->orWhere('company', 'LIKE', "%$search%");
                $query->orWhere('firstname', 'LIKE', "%$search%");
                $query->orWhere('lastname', 'LIKE', "%$search%");
                $query->orWhere('email', 'LIKE', "%$search%");
                $query->orWhere('token', 'LIKE', "%$search%");
            });
        }

        if ($extra && $extra['isActive']) {
            $query->where('isActive', true);
        }
    }
}
