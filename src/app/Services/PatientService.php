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

        $name = array_get($searchParameters, 'name');
        $lastName = array_get($searchParameters, 'last_name');
        $patientID = array_get($searchParameters, 'patientID');


        $patients = User::where('userable_type', 'App\Patient' )
                        ->where(function ($query) use($name, $lastName, $patientID) {
                            $query->where('name', 'like', '%'.$name.'%')
                                ->where('last_name', 'like', '%'.$lastName.'%')
                                ->where('ensurance_id', 'like', '%'.$patientID.'%');
                        })
                        ->get();

        


        return $patients;
    }

}