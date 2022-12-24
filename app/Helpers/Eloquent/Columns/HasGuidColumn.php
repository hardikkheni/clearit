<?php

namespace App\Helpers\Eloquent\Columns;

trait HasGuidColumn
{

    public function getFindByGuidBuilder($guid, $includeDeleted = false)
    {
        $q = $this->model;
        if (!$includeDeleted) {
            $q->withoutTrashed();
        }
        return $q->where('guid', $guid);
    }

    public function findOneByGuid($guid, $includeDeleted = true)
    {
        return $this->getFindByGuidBuilder($guid, $includeDeleted)->first();
    }

    public function findOneOrFailByGuid($guid, $includeDeleted = true)
    {
        return $this->getFindByGuidBuilder($guid, $includeDeleted)->firstOrFail();
    }
}
