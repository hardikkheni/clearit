<?php

namespace App\Repositories\Eloquent;

use App\Helpers\DataTable\HasDataTable;
use App\Models\ISFConsolidator;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class FreightForwarderRepository extends BaseRepository
{

    use HasDataTable;

    const MODEL_LABEL = 'Freight Forwarder';

    public function __construct(
        ISFConsolidator $model
    ) {
        parent::__construct($model);
    }

    protected function search(Builder $query, $search, $extra)
    {
        $query->where('deletedOn', null);
        if ($search) {
            $query->where(function (Builder $query)  use ($search) {
                $query->orWhere('isfcName', 'LIKE', "%$search%");
                $query->orWhere('isfcAddress1', 'LIKE', "%$search%");
                // $query->orWhere('isfcAddress2', 'LIKE', "%$search%");
                $query->orWhere('isfcCountry', 'LIKE', "%$search%");
                $query->orWhere('isfcBusinessPhone', 'LIKE', "%$search%");
                $query->orWhere('isfcZip', 'LIKE', "%$search%");
            });
        }
    }

    protected function select(Builder $query, $select)
    {
        $query->with('contacts', function ($q) {
            $q->where('deletedOn', null);
        });
    }

    public function create($data)
    {
        $data['password'] = md5(@$data['password']);
        $data['createdBy'] = auth()->user()->id;
        return parent::create($data);;
    }

    public function edit($id, $data)
    {
        $ff = $this->findOrFail($id);
        $data['modifiedBy'] = auth()->user()->id;
        if (@$data['password']) {
            $data['password'] = md5($data['password']);
        }
        return $this->model->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        $ff = $this->findOrFail($id);
        $ff->fill(['deletedOn' => Carbon::now(), 'deletedBy' => auth()->user()->id])->save();
        // TODO: should we delete their contacts also
        return $ff;
    }
}
