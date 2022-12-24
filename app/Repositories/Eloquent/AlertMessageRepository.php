<?php

namespace App\Repositories\Eloquent;

use App\Helpers\DataTable\HasDataTable;
use App\Models\AlertMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AlertMessageRepository extends BaseRepository
{

    use HasDataTable;

    const MODEL_LABEL = 'Alert Message';

    public function __construct(AlertMessage $model)
    {
        parent::__construct($model);
    }

    public function dataTable($options)
    {
        $page = $options['page'] ?? 1;
        $length = $options['length'] ?? 10;
        $sort = $options['sort'] ?? null;
        $search = $options['search'] ?? null;
        $select = $options['select'] ?? ['*'];
        $extra = $options['extra'] ?? null;
        // TODO: do we need searching with creator name
        // $query = $this->getModel()->whereHas('creator', function ($query) use ($search) {
        //     if ($search) {
        //         $query->where(DB::raw("CONCAT(firstname,' ', lastname)"), 'LIKE', "%$search%");
        //     }
        // },)->with('creator:id,firstname,lastname');
        $query = $this->getModel()->with('creator:id,firstname,lastname')->where('createdBy', auth()->user()->id);
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

    protected function search(Builder $query, $search, $extra)
    {

        $query->where('deletedOn', null);
        if ($search) {
            $query->where(function (Builder $query)  use ($search) {
                $query->where('subject', 'LIKE', "%$search%");
            });
        }

        if ($extra && $extra['isActive']) {
            $query->where('isActive', true);
        }
    }

    public function create($data)
    {
        $data['createdBy'] = auth()->user()->id;
        return parent::create($data);;
    }

    public function edit($id, $data)
    {
        $data['modifiedBy'] = auth()->user()->id;
        return $this->update([['id', $id]], $data);
    }

    public function delete($id)
    {
        $alertMessage = $this->findOrFail($id);
        $alertMessage->fill(['deletedOn' => Carbon::now(), 'deletedBy' => auth()->user()->id])->save();
        return $alertMessage;
    }

    public function findOrFail($id)
    {
        return $this->model->where(['deletedOn' => null, 'id' => $id])->firstOrFail();
    }
}
