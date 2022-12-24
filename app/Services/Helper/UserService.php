<?php

namespace App\Services\Helper;

use App\Models\User;
use App\Repositories\Eloquent\UserAnnualBondDocumentRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class UserService
{
    protected $affiliateRepo;
    protected $uabdRepo;

    public function __construct(
        UserRepository $userRepo,
        UserAnnualBondDocumentRepository $uabdRepo
    ) {
        $this->userRepo = $userRepo;
        $this->uabdRepo = $uabdRepo;
    }

    public function dataTable($data)
    {
        return $this->userRepo->dataTable($data);
    }

    public function create($data)
    {
        $exist = $this->userRepo->affiliateExistByCode($data['affiliateCode']);

        if ($exist) {
            throw ValidationException::withMessages([
                'affiliateCode' => "Affiliate Code alreday exists!"
            ]);
        }

        if (@$data['logofilename']) {
            $file = Storage::putFile(User::LOGOPATH, $data['logofilename']);
            $file = explode('/', $file);
            $file = $file[count($file) - 1];
            $data['logofilename'] = $file;
        } else {
            $data['logofilename'] = null;
        }
        return $this->userRepo->create($data);
    }

    public function findOrFail($id)
    {
        return $this->userRepo->findOrFail($id);
    }

    public function edit($id, $data)
    {
        $affiliate = $this->userRepo->findOrFail($id);
        $exist = $this->userRepo->affiliateExistByCode($data['affiliateCode']);
        if ($exist && $exist->id != $affiliate->id) {
            throw ValidationException::withMessages([
                'affiliateCode' => ['Affiliate Code alreday exists!']
            ]);
        }

        if (isset($data['remove_logo'])) {
            if (@$data['logofilename']) {
                $file = Storage::putFile(User::LOGOPATH, $data['logofilename']);
                $file = explode('/', $file);
                $file = $file[count($file) - 1];
                $data['logofilename'] = $file;
            } else {
                $data['logofilename'] = null;
            }
            unset($data['remove_logo']);
        }
        return $this->userRepo->edit($id, $data);
    }

    public function delete($id)
    {
        return $this->userRepo->delete($id);
    }

    public function unverifiedWithPoaDataTable($data)
    {
        $response = $this->userRepo->unverifiedWithPoaDataTable($data);
        foreach ($response['rows'] as $row) {
            $row->name = $this->userRepo->getNameById($row['id']);
            $bondDocument = $this->uabdRepo->findActualOneByUserId($row['id']);
            if ($bondDocument) {
                $row->bondrequestcompleted = true;
            }
        }
        return $response;
    }
}
