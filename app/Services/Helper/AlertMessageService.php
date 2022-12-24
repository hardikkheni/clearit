<?php

namespace App\Services\Helper;

use App\Repositories\Eloquent\AlertMessageRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AlertMessageService
{
    protected $alertMessageRepo;

    public function __construct(AlertMessageRepository $alertMessageRepo)
    {
        $this->alertMessageRepo = $alertMessageRepo;
    }

    public function dataTable($data)
    {
        return $this->alertMessageRepo->dataTable($data);
    }

    public function create($data)
    {

        return $this->alertMessageRepo->create($data);
    }

    public function findOrFail($id)
    {
        return $this->alertMessageRepo->findOrFail($id);
    }

    public function edit($id, $data)
    {
        return $this->alertMessageRepo->edit($id, $data);
    }

    public function delete($id)
    {
        return $this->alertMessageRepo->delete($id);
    }
}
