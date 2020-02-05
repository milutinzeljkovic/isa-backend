<?php

namespace App\Services;

use App\Services\IClinicAdminService;
use Auth;
use App\Clinic;
use App\OperationsRoom;
use App\Appointment;
use App\User;
use App\Doctor;
use App\Operations;
use Carbon\Carbon;

class ClinicAdminService implements IClinicAdminService
{
    function getAllDoctors(){

        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $doctors = Doctor::where('clinic_id', $clinicAdmin->clinic_id)->with('user')->get();

        return $doctors;
    }

    function getAllFacilities(){
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->get()[0];
        $facilities = $clinic->operationRooms;

        return $facilities;
    }

    function getOperations(){
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $operations = Operations::where('clinic_id', $clinicAdmin->clinic_id)
                        ->with(['patient' => function($q) {
                            $q->with('user');
                        }])->get();


        return $operations;



    }



    function getAdminsClinic()
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->get()[0];
        return $clinic;
    }

    function updateClinic(array $newClinicData)
    {
        $clinic = Clinic::where('id', array_get($newClinicData, 'id'))->get()[0];

        $clinic->name = array_get($newClinicData, 'name');
        $clinic->description = array_get($newClinicData, 'description');
        $clinic->clinical_center_id = array_get($newClinicData, 'clinic_center');
        $clinic->address = array_get($newClinicData, 'address');

        $clinic->save();

        return response()->json(['updated' => 'Clinic has been updated'], 201);
    }

    function reserveOperation($operations_room_id, $operation_id)
    {
        $message = array('error' => false, 'message' => '');

        $operation = Operations::find($operation_id);
        $operation->duration = 2;
        $operaitonRoom =  OperationsRoom::find($operations_room_id);

        $result = $operaitonRoom->with(['appointments' => function ($q) use($operation) {
            $q->where('done',0)
                ->where('date', '=', $operation->date);
        }])->get();

        $appointmentsInRoom = Appointment::where('operations_room_id',$operations_room_id)
            ->whereDate('date',$operation->date)
            ->where('done',0)
            ->get();
        
        $operationDateStart = Carbon::parse($operation->date);
        $operationDateEnd = Carbon::parse($operation->date);
        $operationDateEnd->addSeconds($operation->duration);

        foreach ($appointmentsInRoom as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($operationDateStart->greaterThanOrEqualTo($start) && $operationDateStart->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operation beginning is overlapping';
            }
            if($operationDateEnd->greaterThanOrEqualTo($start) && $operationDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operation ending is overlapping';
            }  
        }

        $operationsInRoom = Operations::where('operations_rooms_id',$operations_room_id)
            ->where('date','=',$operation->date)
            ->get();
        
        foreach ($operationsInRoom as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($operationDateStart->greaterThanOrEqualTo($start) && $operationDateStart->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operation beginning is overlapping with antother operation';
            }
            if($operationDateEnd->greaterThanOrEqualTo($start) && $operationDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operation ending is overlapping';
            }  
            if($operation->date == $a->date)
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free';
            }
        }

        if($message['error'] == false)
        {
            $operation->operations_rooms_id = $operaitonRoom->id;
            $operation->save();
            return $operation;
        }
        else
        {
            return $message;
        }



    }

}