<?php

namespace App\Models;

use App\Helpers\Eloquent\Scopes\HasActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory, HasActiveScope;

    protected $guarded = ['id'];

    protected $table = 'user_role';

    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isActive' => 'boolean',
        'isDashboard' => 'boolean'
    ];

    public function scopeDashboard($query)
    {
        return $query->where('isDashboard', true);
    }
}
