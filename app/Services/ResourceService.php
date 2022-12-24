<?php

namespace App\Services;

use App\Repositories\Eloquent\ResourceRepository;
use App\Repositories\Eloquent\TicketStatus2Repository;
use App\Repositories\Eloquent\UserRoleRepository;

class ResourceService
{

    protected $resorceRepo;
    protected $userRoleRepo;
    protected $ticketStatus2Repo;

    public function __construct(ResourceRepository $resorceRepo, UserRoleRepository $userRoleRepo, TicketStatus2Repository $ticketStatus2Repo)
    {
        $this->resorceRepo = $resorceRepo;
        $this->userRoleRepo = $userRoleRepo;
        $this->ticketStatus2Repo = $ticketStatus2Repo;
    }

    public function allCountries()
    {
        $countries = $this->resorceRepo->allCountires();
        return $countries->map(function ($country) {
            $country->text = $country->countryName;
            $country->value = $country->alpha2;
            return $country;
        });
    }

    public function getTicketStatus2()
    {
        return $this->ticketStatus2Repo->getAllStatus();
    }
}
