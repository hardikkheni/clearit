<?php

namespace App\Helpers\Eloquent\Columns;

use Illuminate\Support\Facades\DB;

trait HasDisplayOrderColumn
{
    public function shiftFromDisplayOrder($displayOrder, $place = 1)
    {
        $this->model->where('displayOrder', '>=', (int) $displayOrder)->update(['displayOrder' => DB::raw("`displayOrder` + $place")]);
    }
}
