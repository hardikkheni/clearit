<?php

namespace App\Repositories\Eloquent;

use App\Helpers\Eloquent\Columns\HasGuidColumn;
use App\Helpers\Eloquent\Columns\HasUserColumn;
use App\Models\Payment;

class PaymentRepository extends BaseRepository
{
    use HasGuidColumn, HasUserColumn;

    const MODEL_LABEL = 'Payment';

    public function __construct(Payment $model)
    {
        parent::__construct($model);
    }
}
