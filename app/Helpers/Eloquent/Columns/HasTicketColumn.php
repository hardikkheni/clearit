<?php

namespace App\Helpers\Eloquent\Columns;

trait HasTicketColumn
{
    public function findOneByTicketId($id, $select = ['*'])
    {
        $q = $this->model->where('ticketid', $id)->select($select);
        if ($this->model->timestamps) {
            $q->latest();
        }
        return $q->first();
    }

    public function findByTicketId($id, $select = ['*'])
    {
        $q = $this->model->where('ticketid', $id)->select($select);
        if ($this->model->timestamps) {
            $q->latest();
        }
        return $q->get();
    }
}
