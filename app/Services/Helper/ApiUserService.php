<?php

namespace App\Services\Helper;

use App\Repositories\Eloquent\ApiUserRepository;
use Illuminate\Validation\ValidationException;

class ApiUserService
{

    protected $apiUserRepo;

    public function __construct(ApiUserRepository $apiUserRepo)
    {
        $this->apiUserRepo = $apiUserRepo;
    }

    public function create($data)
    {
        return $this->apiUserRepo->create($data);
    }

    public function dataTable($data)
    {
        return $this->apiUserRepo->dataTable($data);
    }

    public function findOrFail($id)
    {
        return $this->apiUserRepo->findOrFail($id);
    }

    public function edit($id, $data)
    {
        $this->apiUserRepo->findOrFail($id);
        return $this->apiUserRepo->edit($id, $data);
    }
}
