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
use App\Mail\AppointmentReservedMail;
use Carbon\Carbon;
use App\Mail\AddToOperationMail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;


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
                        ->with('doctors')
                        ->with(['patient' => function($q) {
                            $q->with('user');
                        }])->get();

    
        $newOperations = array();
        foreach($operations as $o){
            if(count($o->doctors()->get()) == 0){
                array_push($newOperations, $o);
            }

        }

        
     


        return $newOperations;



    }

    function addDuration(array $userData){
        $duration = array_get($userData, 'duration');

        $operation_id = array_get($userData, 'operation_id');
        $operation = Operations::where('id', $operation_id)->first();

        $operation->duration=$duration;
        $operation->save();


        return response()->json(['updated' => 'Operation has been updated'], 201);



    }

    function editOperation(array $userData){


        $doctors = array_get($userData, 'doctors');
        $operation_id = array_get($userData, 'operation_id');
        $operation = Operations::where('id', $operation_id)->first();
        $room=OperationsRoom::where('id',$operation->operations_rooms_id)->first();
        foreach($doctors as $d){

            $doctor= Doctor::where('id',$d['id'])->first();
            $user=$doctor->user()->first();
            

            \Mail::to($user)->send(new AddToOperationMail($user, $operation,$room));
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
        $operaitonRoom =  OperationsRoom::find($operations_room_id);


        $appointmentsInRoom = Appointment::where('operations_room_id',$operations_room_id)
            ->where('date','>',Carbon::now())
            ->where('done',0)
            ->get();
        
        
        $operationDateStart = Carbon::parse($operation->date);
        $operationDateEnd = Carbon::parse($operation->date);
        $operationDateEnd->addSeconds($operation->duration * 3600);
        foreach ($appointmentsInRoom as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($operationDateStart->greaterThanOrEqualTo($start) && $operationDateStart->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free1';
            }
            if($operationDateEnd->greaterThanOrEqualTo($start) && $operationDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free2';
            }  
            if($operationDateStart->lessThanOrEqualTo($start) && $operationDateEnd->greaterThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free3';
            }  
        }

        $operationsInRoom = Operations::where('operations_rooms_id',$operations_room_id)
                ->where('date','>',Carbon::now())
                ->get();
                
        foreach ($operationsInRoom as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($operationDateStart->greaterThanOrEqualTo($start) && $operationDateStart->lessThan($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free4';
            }
            if($operationDateEnd->greaterThan($start) && $operationDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free5';
            }
            if($operationDateStart->lessThanOrEqualTo($start) && $operationDateEnd->greaterThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free6';
            }    
            if($operation->date == $a->date)
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free7';
            }
        }

        if($message['error'] == false)
        {
            $operaiton_room_id = $operaitonRoom->id;
            DB::transaction(function () use($operation, $ooperaiton_room_id){
                DB::table('operations')
                    ->where('id',$operaiton_room_id)
                    ->where('lock_version',$operation->lock_version)
                    ->update(['operations_rooms_id' => $operaiton_room_id]);
                DB::table('operations')
                    ->where('id', $operaiton_room_id)
                    ->update(['lock_version', $operation->lock_version+1]);
            });
            if($operaiton_room_id !== $oeration->operations_rooms_id)
            {
                return response('Error', 400);
            }
            else
            {
                return $operation;
            }            
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
            ->where('date','>',Carbon::now())
            ->where('done',0)
            ->get();
        
        $operationDateStart = Carbon::parse($appointment->date);
        $operationDateEnd = Carbon::parse($appointment->date);
        $operationDateEnd->addSeconds($appointment->duration* 3600);

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
            $patient = $appointment->patient_id;
            $clinic = Clinic::find($appointment->clinic_id);

            $doctor = User::where('userable_id',$appointment->doctor_id)
                ->where('userable_type','App\\Doctor')
                ->first();
            $user = User::where('userable_id',$patient)
                ->where('userable_type','App\\Patient')
                ->first();
            $encrypted = Crypt::encryptString($appointment->id);
            \Mail::to($user)->send(new AppointmentReservedMail($user,$appointment,$doctor,$operaitonRoom,$clinic,$encrypted));

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
            ->where('patient_id','!=',null)
            ->where('operations_room_id','=',null)
            ->with(['doctor' => function($q) {
                $q->with('user');
            }])
            ->with(['patient' => function($q) {
                $q->with('user');
            }])
            ->get(); 
        return $res;
    }

}