<?php

namespace App\Services;

use App\Services\IPatientService;
use App\Clinic;
use App\User;
use App\Doctor;
use App\Patient;
use Auth;
use App\ClinicalCenterAdmin;
use App\MedicalRecord;
use App\Appointment;
use Illuminate\Support\Facades\DB;

use Illuminate\Auth\Access\HandlesAuthorization;



class PatientService implements IPatientService
{

    function getPatientsByClinic()
    {
        $user = Auth::user();
        $medicalStaff = $user->userable()->get()[0];
        
        $clinic = Clinic::where('id', $medicalStaff->clinic_id)->get()[0];
        $patient = $clinic->patients()->with('user')->distinct('id')->get();
        
        return $patient;

    }

    function searchPatients(array $searchParameters)
    {
        $user = Auth::user();
        $doctor = $user->userable()->first();

        $clinic = Clinic::find($doctor->clinic_id);


        $name = array_get($searchParameters, 'name');
        $lastName = array_get($searchParameters, 'last_name');
        $ensurance_id = array_get($searchParameters, 'ensurance_id');

        $patients = $clinic
            ->patients()
            ->with(['user' => function($q) use($name, $lastName, $ensurance_id){
                $q
                ->where('name', 'like', '%'.$name.'%')
                ->where('last_name', 'like', '%'.$lastName.'%')
                ->where('ensurance_id', 'like', '%'.$ensurance_id.'%');
            }])
            ->with(['appointments' => function($q){
                $q
                    ->with('appointmentType')
                    ->where('done',1);
            }])
            ->distinct()
            ->get();

        $array = array();

        foreach($patients as $p)
        {
            if($p->user != null)
            {
                array_push($array,$p);
            }
        }

        return $array;
            
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

    public function getAppointments($id)
    {
        $user = Auth::user();
        $doctor = Doctor::where('id', $user->userable_id)->first();
        $patient = Patient::where('id', $id)->first();

        $appointments = Appointment::where('patient_id', $id)->where('doctor_id', $doctor->id)->where('approved', '=', '1')
        ->with('appointmentType')->get();

        return $appointments;
    }

}