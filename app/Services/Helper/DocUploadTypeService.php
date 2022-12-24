<?php

namespace App\Services\Helper;

use App\Repositories\Eloquent\DocUploadTypeRepository;

class DocUploadTypeService
{

    protected $docUploadTypeRepo;

    public function __construct(DocUploadTypeRepository $docUploadTypeRepo)
    {
        $this->docUploadTypeRepo = $docUploadTypeRepo;
    }

    public function upsert($data)
    {
        $data['roleBitmask'] = array_sum(@$data['permissions']);
        unset($data['permissions']);
        return $this->docUploadTypeRepo->upsert(['id' => @$data['id']], $data);
    }

    public function modeOfTransportList()
    {
        return $this->docUploadTypeRepo->modeOfTransportList();
    }

    public function getByMotId($id)
    {
        return $this->docUploadTypeRepo->getByMotId($id);
    }

    public function delete($id)
    {
        return $this->docUploadTypeRepo->delete($id);
    }
}
