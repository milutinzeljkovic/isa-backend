<?php

namespace App\Services;

use App\Services\IWorkingDayService;
use App\Doctor;
use App\User;
use Auth;
use App\WorkingDay;


class WorkingDayService implements IWorkingDayService
{

    function getDoctorsWorkingHours($id)
    {

        $user = User::find($id);
        $doctor = Doctor::where('id', $user->userable_id)->get()[0];

        $workingDays = WorkingDay::where('doctor_id', $doctor->id)->get();
        
        return $workingDays;
    }

    function updateDoctorsWorkingHours($id, $data)
    {
        $user = User::find($id);
        $doctor = Doctor::where('id', $user->userable_id)->get()[0];
        
        $workingDays = WorkingDay::where('doctor_id', $doctor->id)->get();
        foreach ($workingDays as $workingDay) {
            switch($workingDay->day)
            {
                case(1):
                    $updateDetails = [
                        'from' => array_get($data, 'mondayFrom'),
                        'to' => array_get($data, 'mondayTo')
                    ];
                    $workingDay->update($updateDetails);
                break;
                case(2):
                    $updateDetails = [
                        'from' => array_get($data, 'tuesdayFrom'),
                        'to' => array_get($data, 'tuesdayTo')
                    ];
                    $workingDay->update($updateDetails);
                break;
                case(3):
                    $updateDetails = [
                        'from' => array_get($data, 'wednesdayFrom'),
                        'to' => array_get($data, 'wednesdayTo')
                    ];
                    $workingDay->update($updateDetails);
                break;
                case(4):
                    $updateDetails = [
                        'from' => array_get($data, 'thursdayFrom'),
                        'to' => array_get($data, 'thursdayTo')
                    ];
                    $workingDay->update($updateDetails);
                break;
                case(5):
                    $updateDetails = [
                        'from' => array_get($data, 'fridayFrom'),
                        'to' => array_get($data, 'fridayTo')
                    ];
                    $workingDay->update($updateDetails);
                break;
                case(6):
                    $updateDetails = [
                        'from' => array_get($data, 'saturdayFrom'),
                        'to' => array_get($data, 'saturdayTo')
                    ];
                    $workingDay->update($updateDetails);
                break;
                case(0):
                    $updateDetails = [
                        'from' => array_get($data, 'sundayFrom'),
                        'to' => array_get($data, 'sundayTo')
                    ];
                    $workingDay->update($updateDetails);
                break;
                default:
            }
        }
        return response()->json(['message' => 'Working hours successfully updated'], 200);
    }
}