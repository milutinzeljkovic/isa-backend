<?php

namespace App\Services;

use App\Services\IPatientService;
use App\Clinic;
use App\User;
use Auth;
use App\ClinicalCenterAdmin;

use Illuminate\Auth\Access\HandlesAuthorization;



class PatientService implements IPatientService
{

    function getPatientsByClinic()
    {
        $user = Auth::user();
        $medicalStaff = $user->userable()->get()[0];
        
        $clinic = Clinic::where('id', $medicalStaff->clinic_id)->get()[0];
        $patient = $clinic->patients()->get();

        return $patient;

    }

}