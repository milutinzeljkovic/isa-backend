<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MedicalStaffPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function addClinicAdmin()
    {
        return auth()->user()->userable_type === "App\ClinicalCenterAdmin";
    }

    public function addClinicalCenterAdmin()
    {
        return auth()->user()->userable_type === "App\ClinicalCenterAdmin";
    }

    
}
