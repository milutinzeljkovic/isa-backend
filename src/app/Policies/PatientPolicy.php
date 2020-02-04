<?php

namespace App\Policies;

use App\User;
use App\Patient;
use App\MedicalRecord;
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

    public function viewMedicalRecord(User $user, $id)
    {
        $medicalRecord = MedicalRecord::where('patient_id',$id)->first();
        $patient = Patient::find($id);
        $ret = $user->userable_type == "App\Doctor" || $user->userable_type == "App\Nurse" || $user->userable_id == $id
        || $user->userable_type == "App\ClinicAdmin" || $user->userable_type == "App\ClinicalCenterAdmin";
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
