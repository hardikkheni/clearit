<?php

namespace App\Repositories\Eloquent;

use App\Helpers\DataTable\HasDataTable;
use App\Helpers\Eloquent\Columns\HasGuidColumn;
use App\Models\AgentPermission;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgentRepository extends BaseRepository
{
    use HasDataTable, HasGuidColumn;

    const MODEL_LABEL = 'Agent';

    protected $agentPermission;

    public function __construct(User $model, AgentPermission $agentPermission)
    {
        parent::__construct($model);
        $this->agentPermission = $agentPermission;
    }

    public function findByEmail($email)
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * @param mixed  $options 
     */
    public function dataTable($options)
    {
        $page = $options['page'] ?? 1;
        $length = $options['length'] ?? 10;
        $sort = $options['sort'] ?? null;
        $search = $options['search'] ?? null;
        $select = $options['select'] ?? ['*'];
        $extra = $options['extra'] ?? null;
        $query = $this->getModel()->query();
        $query->where('isAgent', true);
        $total = $query->count();
        $this->search($query, $search, $extra);
        $filtered = $query->count();
        $this->select($query, $select);
        $this->sortBy($query, $sort);
        $data = $query->skip(($page - 1) * $length)->take($length)->get();
        return [
            'rows' => $data,
            'total' => $total,
            'filtered' => $filtered
        ];
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
        return $this->model->where('id', $id)->update($data);
    }

    protected function search(Builder $query, $search, $extra)
    {

        if ($search) {
            $query->where(function (Builder $query)  use ($search) {
                $query->orWhere('login', 'LIKE', "%$search%");
                $query->orWhere('firstname', 'LIKE', "%$search%");
                $query->orWhere('lastname', 'LIKE', "%$search%");
                $query->orWhere('city', 'LIKE', "%$search%");
                $query->orWhere('state', 'LIKE', "%$search%");
                $query->orWhere('email', 'LIKE', "%$search%");
            });
        }

        if ($extra && $extra['isActive']) {
            $query->where('isActive', true);
        }
    }

    public function allAgents()
    {
        return $this->model->agent()->get();
    }

    public function allPermissions()
    {
        return $this->agentPermission->all();
    }

    public function savePermissions($id, $permissions)
    {
        $agent =  $this->findOrFail($id);
        $agent->permissionBitmask = array_sum($permissions);
        return $agent->save();
    }

    public function allInternalAgents()
    {
        return $this->model->agent()->active()->displayInternally()->orderBy('firstname', 'ASC')->get();
    }
}
