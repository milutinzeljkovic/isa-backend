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
        $patient = $clinic->patients()->with('user')->get();
        

        return $patient;

    }

    function searchPatients(array $searchParameters)
    {
        $patients = User::where('userable_type', 'App\Patient');

        $name = array_get($searchParameters, 'name');
        if($name != null){
            $patients = User::where('name', 'like', '%'.$name.'%')->get();
        }

        $lastName = array_get($searchParameters, 'last_name');
        if($lastName != null){
            $patients = $patients->where('last_name', 'like', '%'.$lastName.'%')->get();
        }

        $patientID = array_get($searchParameters, 'patientID');
        if($patientID != null){
            $patients = $patients->where('ensurance_id', 'like', '%'.$patientID.'%')->get();
        }

        return $patients;
    }

}