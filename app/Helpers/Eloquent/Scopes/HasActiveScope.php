<?php

namespace App\Helpers\Eloquent\Scopes;

trait HasActiveScope
{
    public function scopeActive($query)
    {
        return $query->where('isActive', true);
    }
}
