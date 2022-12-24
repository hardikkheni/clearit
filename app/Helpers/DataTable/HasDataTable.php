<?php

namespace App\Helpers\DataTable;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait HasDataTable
{
    /**
     * @param mixed  $options
     */
    public function dataTable($options)
    {
        return $this->useDataTable($options, function (Builder $query, $search, $extra) {
            $this->search($query, $search, $extra);
        });
    }

    /**
     * @param mixed  $options
     * @param \Closure  $search
     * @param \Closure|null  $select
     * 
     * @return array
     */
    public function useDataTable($options, Closure $searchCb, Closure $selectCb = null)
    {
        $page = $options['page'] ?? 1;
        $length = $options['length'] ?? 10;
        $sort = $options['sort'] ?? null;
        $search = $options['search'] ?? null;
        $select = $options['select'] ?? ['*'];
        $extra = $options['extra'] ?? null;
        $query = $this->getModel()->query();
        $total = $query->count();
        $searchCb($query, $search, $extra);
        $filtered = $query->count();
        if ($selectCb) {
            $selectCb($query, $select);
        } else {
            $this->select($query, $select);
        }
        $this->sortBy($query, $sort);
        $data = $query->skip(($page - 1) * $length)->take($length)->get();
        return [
            'rows' => $data,
            'total' => $total,
            'filtered' => $filtered
        ];
    }


    /**
     * @param Builder  $query
     * @param mixed  $search
     * @param mixed  $extra
     */
    abstract protected function search(Builder $query, $search, $extra = null): Builder;

    /**
     * @param Builder  $query
     * @param mixed  $select
     */
    protected function select(Builder $query, $select): Builder
    {
        return $query->select($select);
    }

    protected function getModel()
    {
        return $this->model;
    }

    protected function sortBy(Builder $query, $sort): Builder
    {
        if ($sort && $sort['order'] && $sort['column']) {
            $query->orderBy($sort['column'], $sort['order']);
        }
        return $query;
    }
}
