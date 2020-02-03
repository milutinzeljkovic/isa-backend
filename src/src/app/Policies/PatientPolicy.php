<?php

namespace App\Policies;

use App\User;
use App\Patient;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientPolicy
{
    use HandlesAuthorization;

    public function fetch()
    {
        return auth()->user()->userable_type === "App\ClinicalCenterAdmin";
    }


    /**
     * Determine whether the user can view the patient.
     *
     * @param  \App\User  $user
     * @param  \App\Patient  $patient
     * @return mixed
     */
    public function view(User $user, $patient)
    {
        $ret = $user->userable_type === "App\ClinicalCenterAdmin"  || $user->id === (int)$patient ? true : false;
        return $ret;
    }

    /**
     * Determine whether the user can create patients.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

        /**
     * Determine whether the user can activate the patient.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function accept()
    {
        return auth()->user()->userable_type === "App\ClinicalCenterAdmin";
    }

    public function decline()
    {
        return auth()->user()->userable_type === "App\ClinicalCenterAdmin";
    }



    /**
     * Determine whether the user can update the patient.
     *
     * @param  \App\User  $user
     * @param  \App\Patient  $patient
     * @return mixed
     */
    public function update(User $user, $patient)
    {
        $ret = $user->userable_type === "App\ClinicalCenterAdmin"  || $user->id === (int)$patient ? true : false;
        return $ret;
    }

    /**
     * Determine whether the user can delete the patient.
     *
     * @param  \App\User  $user
     * @param  \App\Patient  $patient
     * @return mixed
     */
    public function delete(User $user, Patient $patient)
    {
        //
    }

    /**
     * Determine whether the user can restore the patient.
     *
     * @param  \App\User  $user
     * @param  \App\Patient  $patient
     * @return mixed
     */
    public function restore(User $user, Patient $patient)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the patient.
     *
     * @param  \App\User  $user
     * @param  \App\Patient  $patient
     * @return mixed
     */
    public function forceDelete(User $user, Patient $patient)
    {
        //
    }
}
