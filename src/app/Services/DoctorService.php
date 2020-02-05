<?php

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\IDoctorService;
use App\Doctor;
use App\Appointment;
use App\User;
use App\WorkingDay;
use App\MedicalReport;
use App\MedicalRecord;
use App\Medicine;
use App\Prescription;
use App\Diagnose;
use App\Operations;


use Auth;
use Log;



class DoctorService implements IDoctorService
{
    function showDoctor($id)
    {
        return Doctor::find($id)->with('appointments')->first();
    }

    function showDoctorsAppointments($id)
    {
        $result = Doctor::with('user')->with(['appointments' => function ($q) {
            $q->where('patient_id','=',null)
              ->where('date', '>', Carbon::createFromTimestamp(Carbon::now()->getTimestamp())->toDateTimeString())
                ->with('operationsRoom')
                ->with('appointmentType')
                ->with('doctor.user');
        }])
        ->with('appointmentTypes')
        ->where('id',$id)
        ->first();

        return $result;
    }

    function searchDoctors($clinic_id, $name, $date, $stars, $appointmentType)
    {
        $searchByName = ($name == null ? false : true);
        $searchByDate = ($date == null ? false : true);
        $searchByStars = ($stars == null ? false : true);
        $searchByType = ($appointmentType == null ? false : true);
        $results = DB::table('doctors')
            ->where('doctors.clinic_id', $clinic_id)
            ->when($searchByDate, function($query) use ($date, $searchByDate, $searchByType, $appointmentType){
                $query->join('appointments', function ($join) use ($searchByDate, $date, $searchByType, $appointmentType){
                    $join->on('doctors.id', '=', 'appointments.doctor_id')
                    ->when($searchByDate, function($query) use ($date, $searchByType, $appointmentType){
                        $query
                            ->whereDate('appointments.date', '=', $date)
                            ->where('appointments.patient_id',null)
                            ->when($searchByType, function($query) use ($appointmentType){
                                $query->where('appointments.appointment_type_id', '=', $appointmentType);
                            });
                            return $query;
                    });
                });
            })
            ->when($searchByType, function($query) use ($appointmentType, $searchByType){
                $query->join('appointment_type_doctor', function ($join) use ($searchByType, $appointmentType){
                    $join->on('doctors.id', '=', 'appointment_type_doctor.doctor_id')
                    ->when($searchByType, function($query) use ($appointmentType){
                        return $query->where('appointment_type_doctor.appointment_type_id', '=', $appointmentType);
                    });
                });
            })
            ->when(true, function($query) use ($name, $searchByName){
                $query->join('users', function ($join) use ($searchByName, $name){
                    $join->on('doctors.id', '=', 'users.userable_id')
                    ->where('users.userable_type' ,'=', 'App\\Doctor')
                    ->when($searchByName, function($query) use ($name){
                        return $query->where('users.name', 'like', '%'.$name.'%');
                    });
                });
            })
            ->when($searchByStars, function($query) use ($stars){
                return $query->where('stars_count', '=', $stars);
            })
            ->select('doctors.id','users.name','users.email','users.last_name','doctors.stars_count')
            ->groupBy('doctors.id')
            ->get();

            return $results;
    }

    function getApointments()
    {
        $user = Auth::user();
        $doctor = $user->userable()->get()[0];
        $appointments = Appointment::where('doctor_id', $doctor->id)
                                    ->where('patient_id','!=',null)
                                    ->where('approved',1)
                                    ->with('appointmentType')
                                    ->with(['patient' => function($q) {
                                        $q->with('user');
                                    }])
                                    ->get();

        

        return $appointments;
    }

    function getOperations()
    {
        $user = Auth::user();
        $doctor = $user->userable()->get()[0];

        $operations = $doctor->operations() 
                            ->with(['patient' => function($q) {
                                $q->with('user');
                            }])
                            ->get();
        

        return $operations;
    }

    function medicalReportForAppointment(array $userData)
    {
        
        $height = array_get($userData, 'height');
        $weight = array_get($userData, 'weight');
        $allergy = array_get($userData, 'allergy');
        $diopter = array_get($userData, 'diopter');
        $bloodType = array_get($userData, 'blood_type');
        $therapy = array_get($userData, 'therapy');
        $diagnose = array_get($userData, 'diagnose');
        $medicines = array_get($userData, 'medicines');
        $appointment_id = array_get($userData, 'appointment_id');

        


        $appointment =  Appointment::where('id',$appointment_id )->first();
        $medicalRecord =  MedicalRecord::where('patient_id', $appointment->patient_id)->first();
        if($medicalRecord == null)
        {
            $medicalRecord = new MedicalRecord();
            $medicalRecord->patient_id = $appointment->patient_id;
        }


        if( $appointment->done == 1){

            $medicalRecord->height=$height;
            $medicalRecord->weight=$weight;
            $medicalRecord->allergy=$allergy;
            $medicalRecord->diopter=$diopter;
            $medicalRecord->blood_type=$bloodType;
            $medicalRecord->save();
            return response()->json(['created' => 'Medical record has been changed'], 201);
        }

        $medicalRecord->height=$height;
        $medicalRecord->weight=$weight;
        $medicalRecord->allergy=$allergy;
        $medicalRecord->diopter=$diopter;
        $medicalRecord->blood_type=$bloodType;
        $medicalRecord->save();

        $d = Diagnose::where('label',$diagnose['label'])->first();
        $medicalReport= new MedicalReport();
        $medicalReport->medical_record_id=$medicalRecord->id;
        $medicalReport->diagnose_id=$d->id;
        $medicalReport->information=$therapy;
        $medicalReport->doctor_id=$appointment->doctor_id;
        $medicalReport->appointment_id=$appointment->id;
        $medicalReport->save();

        foreach($medicines as $med){
        
                $medicine = Medicine::where('label',$med['label'])->first();
                $prescription = new Prescription();
                $prescription->medical_report_id=$medicalReport->id;
                $prescription->medicine_id=$medicine->id;
                $prescription->info=$med['info'];
                $prescription->save();
            
        }

        $appointment->done=1;
        $appointment->save();

        return response()->json(['created' => 'Medical report has been created'], 201);



    }

    function getDataForDoctor($appointment_id)
    {
        $appointment =  Appointment::where('id',$appointment_id )->first();

        if($appointment->done == 0){
            $medicalRecord =  MedicalRecord::where('patient_id', $appointment->patient_id)->first();
            return $medicalRecord;
        }

        $medicalReport= MedicalReport::where('appointment_id',$appointment_id)
                        ->with('diagnose')
                        ->with('medicalRecord')
                        ->with('appointment')
                        ->with(['prescriptions' => function($q) {
                            $q->with('medicine');
                        }])               
                         ->first();

        return $medicalReport;
                    
                        
    }

    


    function sheduleAnOperation(array $userData)
    {
        $user = Auth::user();
        $doctor = $user->userable()->get()[0];

        
        $appointment_id = array_get($userData, 'appointment_id');
        $date = array_get($userData, 'date');
        $info = array_get($userData, 'info');

        $appointment =  Appointment::where('id',$appointment_id )->first();

        $operation = new Operations;
        $operation->clinic_id=$doctor->clinic_id;
        $operation->patient_id=$appointment->patient_id;
        $operation->info=$info;

        $operation->date=$date;

        $operation->save();

        return response()->json(['created' => 'Operation has been created'], 201);


    }



    function seeIfDoctorUsed($id)
    {
        $user = User::find($id);
        $doctor = Doctor::where('id', $user->userable_id)->get()[0];

        $allApps = Appointment::all();

        foreach($allApps as $appointment){
            if($appointment->doctor_id == $doctor->id){    //za sad ne proverava da li je termin zakazan
                return response()->json(["true"], 200);
            }
        }

        return response()->json(["false"], 200);
    }

    function getWorkingHours($id){
        $user = User::find($id);

        $workingHours = WorkingDay::where('doctor_id', $user->userable_id)->get();
        
    }
}