<?php

namespace App\Services;

use App\Services\IClinicAdminService;
use Auth;
use App\Clinic;
use App\User;
use App\Doctor;

class ClinicAdminService implements IClinicAdminService
{
    function getAllDoctors(){

        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $doctors1 = Doctor::where('clinic_id', $clinicAdmin->clinic_id)->get()[0];
        
        $user1 = $doctors1->with('user')->get();
        //$doctors = User::where('userable_id', $doctors1::all()->id)->get()[0];

        return $user1;
    }

    function getAllFacilities(){
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->get()[0];
        $facilities = $clinic->operationRooms;

        return $facilities;
    }


    function getClinicDetails()
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->get()[0];
        return $clinic;
    }
    
    //function defineAvailableAppointment();
}