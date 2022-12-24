<?php

namespace App\Repositories\Eloquent;

use App\Models\ISFConsolidatorContact;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;

class FFContactRepository extends BaseRepository
{

    const MODEL_LABEL = 'Freight Forwarder Contact';

    public function __construct(
        ISFConsolidatorContact $model
    ) {
        parent::__construct($model);
    }

    public function create($data)
    {
        $data['createdBy'] = auth()->user()->id;
        $data['isDefault'] ??= false;
        return parent::create($data);;
    }

    public function findOrFail($id)
    {
        return $this->model->where(['deletedOn' => null, 'id' => $id])->firstOrFail();
    }


    public function edit($id, $data)
    {
        $fc = $this->findOrFail($id);
        $data['modifiedBy'] = auth()->user()->id;
        $fc->fill($data)->save();
        return $fc;
    }

    public function delete($id)
    {
        $fc = $this->findOrFail($id);
        $fc->fill(['deletedOn' => Carbon::now(), 'deletedBy' => auth()->user()->id])->save();
        return $fc;
    }
    public function findByIsfConsolidatorId($isfConsolidatorId)
    {
        return $this->findAndOrSort([['isfConsolidatorId', $isfConsolidatorId]])->get();
    }
}
