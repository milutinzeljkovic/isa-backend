<?php

namespace App\Services;

use App\Services\IPatientService;
use App\Clinic;
use App\User;
use Auth;
use App\ClinicalCenterAdmin;
use App\MedicalRecord;

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
        $ensurance_id = array_get($searchParameters, 'ensurance_id');

        $patients = User::where('userable_type', 'App\Patient' )
                        ->where(function ($query) use($name, $lastName, $ensurance_id) {
                            $query->where('name', 'like', '%'.$name.'%')
                                ->where('last_name', 'like', '%'.$lastName.'%')
                                ->where('ensurance_id', 'like', '%'.$ensurance_id.'%');
                        })
                        ->get();

        
        return $patients;
    }

    function getMedicalRecord($id)
    {
        if(Auth::user()->userable_type != "App\Patient")
        {
            return [];
        }
        return $medicalRecord = MedicalRecord::with('medicalDatas')
            ->with(['medicalReports' => function($q) {
                $q->with('diagnose')
                  ->with('therapy')
                  ->with(['appointment' => function($q) {
                    $q->with('clinic');
                  }])
                  ->with(['doctor' => function($q) {
                      $q->with('user');
                  }])
                  ->with(['prescriptions' => function($q){
                    $q->with('medicine')
                      ->with(['nurse' => function($q) {
                        $q->with('user');
                      }]);
                  }]);

        }])
            ->where('patient_id',$id)->first();
    }

}