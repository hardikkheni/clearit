<?php

namespace App\Helpers\Eloquent\Columns;

trait HasUserColumn
{

    public function findByUserId($id)
    {
        return $this->model->where('userid', $id)->get();
    }

    public function findOneByUserId($id)
    {
        return $this->model->where('userid', $id)->first();
    }

    public function countByUserId($id)
    {
        return $this->model->where('userid', $id)->count();
    }
}
