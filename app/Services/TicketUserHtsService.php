<?php

namespace App\Services;

use App\Repositories\Eloquent\HtsCodeLongDescriptionRepository;
use App\Repositories\Eloquent\TicketRepository;
use App\Repositories\Eloquent\TicketUserHtsRepository;
use App\Repositories\Eloquent\UserHtsRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Eloquent\UserRoleRepository;
use Illuminate\Support\Facades\DB;

class TicketUserHtsService
{

    protected TicketUserHtsRepository $tuhRepo;
    protected UserHtsRepository $uhRepo;
    protected UserRepository $userRepo;
    protected HtsCodeLongDescriptionRepository $hcldRepo;
    protected UserRoleRepository $userRoleRepo;
    protected TicketRepository $ticketRepo;

    public function __construct(
        TicketUserHtsRepository $tuhRepo,
        UserHtsRepository $uhRepo,
        UserRepository $userRepo,
        HtsCodeLongDescriptionRepository $hcldRepo,
        UserRoleRepository $userRoleRepo,
        TicketRepository $ticketRepo
    ) {
        $this->tuhRepo = $tuhRepo;
        $this->uhRepo = $uhRepo;
        $this->userRepo = $userRepo;
        $this->hcldRepo = $hcldRepo;
        $this->userRoleRepo = $userRoleRepo;
        $this->ticketRepo = $ticketRepo;
    }

    public function create($data)
    {
        $user = $this->userRepo->findOneOrFailByGuid($data['guid']);
        $uhts = $this->uhRepo->create([
            'code' => $data['code'],
            'description' => $data['description'],
            'sku' => $data['sku'],
            'userId' => $user['id'],
        ]);
        $tuhts = $this->tuhRepo->create([
            'ticketId' => $data['ticketId'],
            'userHtsId' => $uhts['id'],
        ]);
        $newHtsCode = $data['code'];
        $htsCode = explode('.', $data['code']);
        if (isset($htsCode[2]) && strlen($htsCode[2]) == 4) {
            $newHtsCode = $htsCode[0] . '.' . $htsCode[1] . '.' . substr($htsCode[2], 0, 2) . '.' . substr($htsCode[2], 2, 2);
        }
        $this->hcldRepo->upsertByHtsCode([
            'htsCode' => $newHtsCode,
            'agentDescription' => $data['description'],
        ]);
        return $tuhts;
    }

    public function update($id, $data)
    {
        $tuhts = $this->tuhRepo->findOrFail($id);
        $user = $this->userRepo->findOneOrFailByGuid($data['guid']);
        $this->uhRepo->update([['id', $data['uhtsId']]], [
            'description' => $data['description'],
            'sku' => $data['sku'],
        ]);
        $newHtsCode = $data['code'];
        $htsCode = explode('.', $data['code']);
        if (isset($htsCode[2]) && strlen($htsCode[2]) == 4) {
            $newHtsCode = $htsCode[0] . '.' . $htsCode[1] . '.' . substr($htsCode[2], 0, 2) . '.' . substr($htsCode[2], 2, 2);
        }
        $this->hcldRepo->upsertByHtsCode([
            'htsCode' => $newHtsCode,
            'agentDescription' => $data['description'],
        ]);
        if ($data['role']) {
            $userRole = $this->userRoleRepo->findOneByInteralKey($data['role']);
            $this->ticketRepo->update([['id', $data['ticketId']]], [
                'roleStatusId' => DB::raw(UserRoleRepository::GetTicketRoleStatusFunction . "({$data['ticketId']}, {$userRole['id']})")
            ]);
        }
        return $tuhts;
    }

    public function delete($id)
    {
        $tuhts = $this->tuhRepo->findOrFail($id);
        $tuhts->forceDelete($id);
        return $tuhts;
    }
}
