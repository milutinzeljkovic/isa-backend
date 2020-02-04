<?php

namespace App\Services;

use App\Services\IVacationService;
use Auth;
use App\Vacation;
use App\Doctor;
use App\Nurse;
use App\User; 
use stdClass;
use Carbon\Carbon;

class VacationService implements IVacationService
{
    function getVacationRequests()
    {
        $list = collect();
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];
        $vacations = Vacation::where('approved', -1)->whereDate('from', '>=', Carbon::now())->get();
        
        if($vacations->count() > 0){
            foreach ($vacations as $vacation) {
                $object = new stdClass;
                $user = User::where('id', $vacation->user_id)->get()[0];
                $object->id = $vacation->id;
                $object->name = $user->name;
                $object->last_name = $user->last_name;
                $object->from = $vacation->from;
                $object->to = $vacation->to;
                if($user->userable_type == "App\Doctor"){
                    $doctor = Doctor::where('id', $user->userable_id)->get()[0];
                    $object->role = 'Doctor';
                    if($doctor->clinic_id == $clinicAdmin->clinic_id){
                        $list->push($object);
                    }
                }else {
                    $nurse = Nurse::where('id', $user->userable_id)->get()[0];
                    $object->role = 'Nurse';
                    if($nurse->clinic_id == $clinicAdmin->clinic_id){
                        $list->push($object);
                    }
                }
            }
        }
        
        return $list;
    }

    function approveVacationRequest($id){
        $vacation = Vacation::where('id', $id)->get()[0];
        $vacation->approved = 1;
        $vacation->save();

        return $vacation->id;
    }
}