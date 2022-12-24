<?php

namespace App\Repositories\Eloquent;

use App\Models\UserAnnualBondDocument;

class UserAnnualBondDocumentRepository extends BaseRepository
{
    protected $userRepo;

    const MODEL_LABEL = 'User Annual Bond Document';

    public function __construct(
        UserAnnualBondDocument $model,
        UserRepository $userRepo
    ) {
        parent::__construct($model);
        $this->userRepo = $userRepo;
    }

    public function findActualOneByUserId($userId)
    {
        $bondDoc = $this->model->where('userId', $userId)->orderBy('id', 'DESC')->first();
        if ($bondDoc) {
            $user = $this->userRepo->findById($userId);
            if ($bondDoc && $user && $user->isBondRequested && $user->bondRequestDate && $bondDoc->createdOn >= $user->bondRequestDate) {
                return $bondDoc;
            }
        }
        return null;
    }
}
