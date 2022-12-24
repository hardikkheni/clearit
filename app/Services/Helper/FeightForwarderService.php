<?php

namespace App\Services\Helper;

use App\Repositories\Eloquent\FreightForwarderRepository;
use App\Repositories\Eloquent\FFContactRepository;

class FeightForwarderService
{

    protected $feightForwarderRepo;
    protected $ffContactRepo;

    public function __construct(
        FreightForwarderRepository $feightForwarderRepo,
        FFContactRepository $ffContactRepo
    ) {
        $this->feightForwarderRepo = $feightForwarderRepo;
        $this->ffContactRepo = $ffContactRepo;
    }

    public function dataTable($data)
    {
        return $this->feightForwarderRepo->dataTable($data);
    }

    public function create($data)
    {
        return $this->feightForwarderRepo->create($data);
    }

    public function findOrFail($id)
    {
        $ff = $this->feightForwarderRepo->findOrFail($id);
        $ff->load('contacts');
        return $ff;
    }

    public function edit($id, $data)
    {
        return $this->feightForwarderRepo->edit($id, $data);
    }

    public function delete($id)
    {
        return $this->feightForwarderRepo->delete($id);
    }

    public function createContact($id, $data)
    {
        $this->findOrFail($id);
        $data['isfConsolidatorId'] = $id;
        return $this->ffContactRepo->create($data);
    }

    public function findContactOrFail($id)
    {
        return $this->ffContactRepo->findOrFail($id);
    }

    public function editContact($id, $data)
    {
        return $this->ffContactRepo->edit($id, $data);
    }

    public function deleteContact($id)
    {
        return $this->ffContactRepo->delete($id);
    }

    public function getContacts($id)
    {
        $this->findOrFail($id);
        return $this->ffContactRepo->findByIsfConsolidatorId($id);
    }
}
