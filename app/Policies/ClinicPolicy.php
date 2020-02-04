<?php

namespace App\Policies;

use App\User;
use App\Clinic;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClinicPolicy
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

    public function rate(User $user, $id)
    {
        $clinic = Clinic::find($id);
        $res = $user->userable()->first();
        if($user->userable_type != 'App\\Patient')
        {
            return false;
        }

        if(count($res->appointments()->where('clinic_id',$id)->where('done',1)->get()) == 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function rateDoctor(User $user, $id)
    {
        $clinic = Clinic::find($id);
        $res = $user->userable()->first();
        if($user->userable_type != 'App\\Patient')
        {
            return false;
        }

        if(count($res->appointments()->where('doctor_id',$id)->where('done',1)->get()) == 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function add(User $user)
    {
        if($user->userable_type != 'App\\ClinicalCenterAdmin')
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    public function update(User $user)
    {
        if($user->userable_type != 'App\\ClinicAdmin')
        {
            return false;
        }
        return true;
    }

    public function fetchFacilities(User $user)
    {
        if($user->userable_type != 'App\\ClinicAdmin')
        {
            return false;
        }
        return true;
    }
}