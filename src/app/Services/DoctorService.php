<?php

namespace App\Services;
use App\Services\IDoctorService;
use App\Doctor;
use App\Appointment;
use App\User;
use App\WorkingDay;

class DoctorService implements IDoctorService
{
    function showDoctor($id)
    {
        return Doctor::find($id)->with('appointments')->first();
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