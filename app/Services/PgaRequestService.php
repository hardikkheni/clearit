<?php

namespace App\Services;

use App\Repositories\Eloquent\PgaRequestRepository;

class PgaRequestService
{
    protected $pgaReqRepo;

    public function __construct(
        PgaRequestRepository $pgaReqRepo
    ) {
        $this->pgaReqRepo = $pgaReqRepo;
    }

    public function delete($id)
    {
        return $this->pgaReqRepo->delete($id);
    }
}
