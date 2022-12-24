<?php

namespace App\Repositories\Eloquent;

use App\Models\UserVendor;

class UserVendorRepository extends BaseRepository
{

    const MODEL_LABEL = 'User Vendor';

    public function __construct(UserVendor $model)
    {
        parent::__construct($model);
    }
}
