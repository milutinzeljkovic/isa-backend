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
                        ->where('operations_rooms_id',null)
                        ->with('doctors')
                        ->with(['patient' => function($q) {
                            $q->with('user');
                        }])->get();


        return $operations;



    }

    function editOperation(array $userData){


        $doctors = array_get($userData, 'doctors');
        $duration = array_get($userData, 'duration');
        $operation_id = array_get($userData, 'operation_id');

        $operation = Operations::where('id', $operation_id)->first();

        $operation->duration=$duration;
        $operation->save();

        foreach($doctors as $d){

            $doctor= Doctor::where('id',$d['id'])->first();
            $operation->doctors()->attach($doctor);
        }


        return response()->json(['updated' => 'Operation has been updated'], 201);


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

    function reserveAppointmentRoom($operations_room_id, $appointment_id)
    {
        $message = array('error' => false, 'message' => '');

        $appointment = Appointment::find($appointment_id);
        
        $operaitonRoom =  OperationsRoom::find($operations_room_id);

        if($appointment->duration == null)
        {
            $appointment->duration = 1;
        }

        $appointmentsInRoom = Appointment::where('operations_room_id',$operations_room_id)
            ->where('date','=',$appointment->date)
            ->where('done',0)
            ->get();
        
        $operationDateStart = Carbon::parse($appointment->date);
        $operationDateEnd = Carbon::parse($appointment->date);
        $operationDateEnd->addSeconds($appointment->duration);

        foreach ($appointmentsInRoom as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($operationDateStart->greaterThanOrEqualTo($start) && $operationDateStart->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Appointment beginning is overlapping';
            }
            if($operationDateEnd->greaterThanOrEqualTo($start) && $operationDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Appointment ending is overlapping';
            }  
            if($operationDateStart == $start )
            {
                $message['error'] = true;
                $message['message'] = 'Operating room is not free';
            }
        }

        $operationsInRoom = Operations::where('operations_rooms_id',$operations_room_id)
            ->where('date','=',$appointment->date)
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
                $message['message'] = 'Appointment beginning is overlapping with operation';
            }
            if($operationDateEnd->greaterThanOrEqualTo($start) && $operationDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Appointment ending is overlapping';
            }  
            if($operation->date == $a->date)
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free';
            }
        }

        if($message['error'] == false)
        {
            $appointment->operations_room_id = $operaitonRoom->id;
            $appointment->save();
            return $appointment;
        }
        else
        {
            return $message;
        }


    }

    function pendingAppointmentRequests()
    {
        $user = Auth::user();
        $admin = $user->userable()->first();
        $clinic_id = $admin->clinic_id;
        
        $res = Appointment::where('clinic_id',$clinic_id)
            ->where('done',0)
            ->where('date','>',Carbon::now())
            ->where('operations_room_id','=',null)
            ->get(); 
        return $res;
    }

}