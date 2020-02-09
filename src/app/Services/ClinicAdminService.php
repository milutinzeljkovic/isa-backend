<?php

namespace App\Services;

use App\Services\IClinicAdminService;
use Auth;
use App\Clinic;
use App\OperationsRoom;
use App\Appointment;
use App\AppointmentType;
use App\User;
use App\Doctor;
use App\Operations;
use App\Mail\AppointmentReservedMail;
use App\Mail\ChangeDateOfOperationMail;
use Carbon\Carbon;
use App\Mail\AddToOperationMail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use stdClass;
use DateTime;

use App\ClinicAdmin;
use App\Recension;

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
        
        $c = 0;

        foreach($doctors as $d)
        {
            $c++;
            try
            {

                DB::beginTransaction();

                $res = DB::table('doctor_operations')
                    ->insert(['date' => $operation->date, 'doctor_id' => $d['id']]);
                
                $reservation = DB::table('doctor_operations')
                    ->where('date', '=', $operation->date)
                    ->where('doctor_id', '=', $d['id'])
                    ->where('operations_id', null)
                    ->lockForUpdate()
                    ->first();

                
                if($reservation == null)
                {
                    return response('Error',400);
                }


                DB::table('doctor_operations')
                    ->where('date', '=', $operation->date)
                    ->where('doctor_id', '=', $d['id'])
                    ->update(['operations_id' => $operation->id
                ]);

                 DB::commit();
            }
            catch(\Exception $exception)
            {
                DB::rollback();
                return response('Error',400);
            }

            $doctor= Doctor::where('id',$d['id'])->first();
            $user=$doctor->user()->first();
            \Mail::to($user)->send(new AddToOperationMail($user, $operation,$room));
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

        DB::transaction(function () use($clinic, $newClinicData){
            $cl =  DB::table('clinics')
                ->where('id', array_get($newClinicData, 'id'))
                ->first();
            DB::table('clinics')
                ->where('id', $cl->id)
                ->where('lock_version', $cl->lock_version)
                ->update([
                        'name' => $clinic->name,
                        'description' => $clinic->description,
                        'address' => $clinic->address,
                        'lock_version' => $cl->lock_version +1
                    ]);            
        });

        $updatedClinic = Clinic::find(array_get($newClinicData, 'id'));

        if($clinic->description != $updatedClinic->description)
        {
            return response('Error '.json_encode($updatedClinic),400);
        }
        else
        {
            return response()->json(['updated' => 'Clinic has been updated'], 201);

        }
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
            DB::transaction(function () use($operation, $operaiton_room_id){
                $oper =  DB::table('operations')
                    ->where('id', $operation->id)
                    ->first();
                DB::table('operations')
                    ->where('id', $oper->id)
                    ->where('lock_version', $oper->lock_version)
                    ->update([
                            'operations_rooms_id' => $operaiton_room_id,
                            'lock_version' => $oper->lock_version +1
                        ]);            
            });

            $updatedOperation = Operations::find($operation_id);

            if($operaiton_room_id != $updatedOperation->operations_rooms_id)
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
            if($appointment->date == $a->date)
            {
                $message['error'] = true;
                $message['message'] = 'Operating room not free';
            }
        }

        if($message['error'] == false)
        {
            $patient = $appointment->patient_id;
            $clinic = Clinic::find($appointment->clinic_id);
            $doctor = User::where('userable_id',$appointment->doctor_id)
                ->where('userable_type','App\\Doctor')
                ->first();
            $user = User::where('userable_id',$patient)
                ->where('userable_type','App\\Patient')
                ->first();


            $operaiton_room_id = $operaitonRoom->id;
            DB::transaction(function () use($appointment, $operaiton_room_id)
            {
                $app =  DB::table('appointments')
                    ->where('id', $appointment->id)
                    ->first();
                DB::table('appointments')
                    ->where('id', $app->id)
                    ->where('lock_version', $app->lock_version)
                    ->update([
                            'operations_room_id' => $operaiton_room_id,
                            'lock_version' => $app->lock_version +1,
                        ]);            
            });

            $updatedAppointment = Appointment::find($appointment_id);

            if($operaiton_room_id != $updatedAppointment->operations_room_id)
            {
                return response('Error', 400);
            }
            else
            {
                $encrypted = Crypt::encryptString($appointment->id);
                \Mail::to($user)->send(new AppointmentReservedMail($user,$appointment,$doctor,$operaitonRoom,$clinic,$encrypted));
                return $updatedAppointment;
            } 

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

    function changeDateoOfOperation(array $userData){
        $room_id = array_get($userData, 'room_id');
        $date = array_get($userData, 'date');
        $operation_id = array_get($userData, 'operation_id');
        $room=OperationsRoom::where('id',$room_id)->first();

        $operation = Operations::find($operation_id);
        $oldDate=$operation->date;
        $operation->date=$date;
        $operation->operations_rooms_id=$room_id;
        $operation->save();

        $patient = $operation->patient()->first();
        $user=$patient->user()->first();

       
    

        \Mail::to($user)->send(new ChangeDateOfOperationMail($user,$oldDate, $operation,$room));



        return response()->json(['updated' => 'Date of operation has been updated'], 201);


    }

    function getByAppType($id){
        $appType = AppointmentType::where('id', $id)->get()[0];

        $doctors1 = DB::table('appointment_type_doctor')->where('appointment_type_id', $id)->get();
        $doctors = collect();
        foreach($doctors1 as $doc){
            $user = Doctor::where('id', $doc->doctor_id)->with('user')->first();
            $doctors->push($user);
        }

        return $doctors;
    }

    function specializeDoctor($id, $data){
        $user = User::where('id', $id)->first();
        $doctor = Doctor::where('id', $user->userable_id)->first();

        $collection = collect();
        foreach($data as $detail){
            $at = AppointmentType::where('id', $detail['id'])->first();
            $collection->push($at);
        }
        
        if(count($data) > 0) {
            foreach($collection as $detail){
                $doctor->appointmentTypes()->save($detail);
            }

            return response()->json(['message' => 'Doctor is now more specialized']);
        }
        
        return response()->json(['message' => 'Doctor did not learn anything new']);
    }

    function updateAppointmentRequest($data){
        $discount = (int)(explode('%', array_get($data, 'discount'))[0]);
        $duration = (int)(explode(' ', array_get($data, 'duration'))[0]);

        if($discount > 99){
            return response()->json(['message' => 'Bad request'], 401);
        }
        $id = (int)(array_get($data, 'app'));

        $appointment = Appointment::where('id', $id)->first();
        $appointment->discount = $discount;
        $appointment->duration = $duration;

        $appointment->save();

        return response()->json(['message' => 'Successfully updated appointment request'], 201);
    }

    function getAverageClinicRating()
    {
        $user = Auth::user();
        $clinicAdmin = ClinicAdmin::where('id', $user->userable_id)->first();

        $recensions = Recension::where('clinic_id', $clinicAdmin->clinic_id)->get();

        $sum = 0;
        foreach($recensions as $recension){
            $sum = $sum + $recension->stars_count;
        }

        return round($sum/count($recensions),2);
    }

    function getAverageRatingDoctor(){
        $user = Auth::user();
        $clinicAdmin = ClinicAdmin::where('id', $user->userable_id)->first();
        $doctors = Doctor::where('clinic_id', $clinicAdmin->clinic_id)->get();
        $retVal = array();
        
        foreach($doctors as $doctor){
            $object = new stdClass;
            $recensions = Recension::where('doctor_id', $doctor->id)->get();
            $user1 = User::where('userable_type', 'App\Doctor')->where('userable_id', $doctor->id)->first();

            $sum = 0;
            if(count($recensions) == 0){
                $object->id = $user1->id;
                $object->rating = 0;
                $retVal[] = $object;
                continue;
            }
    
            foreach($recensions as $recension){
                $sum = $sum + $recension->stars_count;
            }

            $object->id = $user1->id;
            $object->rating = round($sum/count($recensions),2);
            $retVal[] = $object;
        }
        
        return $retVal;
    }

    function earnedMoneyInPeriod($data){
        $start = array_get($data, 'start');
        $end = array_get($data, 'end');
        $user = Auth::user();
        $clinicAdmin = ClinicAdmin::where('id', $user->userable_id)->first();

        $appointments = Appointment::where('date', '>=', $start)->where('date', '<=', $end)->
                                     where('clinic_id',$clinicAdmin->clinic_id)->get();

        $sum = 0;
        if(count($appointments) > 0){
            foreach($appointments as $appointment){
                $moneyEarned = ((int)$appointment->price * (100 - (int)$appointment->discount))/100;
                $sum = $sum + $moneyEarned;
            }
        }

        return $sum;
    }

    function weeklyNumberOfAppointments()
    {
        $user = Auth::user();
        $clinicAdmin = ClinicAdmin::where('id', $user->userable_id)->first();

        $dateToday = date("Y-m-d");
        $year = (int)explode('-', $dateToday)[0];
        if((int)date("W") - 9 < 0){
            $week = 52 - (9 - (int)date("W"));
            $year = $year - 1;
        }else {
            $week = (int)date("W") - 9;
        }

        $retVal = array();
        $dates = array();
        for($i = 0; $i < 10; $i++){
            $object = new stdClass;
            $dto = new DateTime();
            $startDate = $dto->setISODate($year, $week)->format('Y-m-d');
            $endDate = $dto->modify('+6 days')->format('Y-m-d');
            $apps = Appointment::where('done', '=', '1')->where('clinic_id', $clinicAdmin->clinic_id)->where('done', '=', '1')->whereDate('date','>=', $startDate)->whereDate('date','<=', $endDate)->get();    
            if($week + 1 > 52){
                $week = 1;
                $year++;
            }else {
                $week++;
            }
            $object->x = 'Week '.' '.$week.' - '.substr($year,-2);
            $object->y = count($apps);
            $retVal[] = $object;
        }
        return $retVal;
    }

    function monthlyNumberOfAppointments()
    {
        $user = Auth::user();
        $clinicAdmin = ClinicAdmin::where('id', $user->userable_id)->first();
        $year = (int)explode('-', date("Y-m-d"))[0]; //current date
        $month = (int)explode('-', date("Y-m-d"))[1];
        $day = (int)explode('-', date("Y-m-d"))[2];

        $yearX = $year - 1;
        $arrayMonths = array();
        $arrayYear = array();
        $numberOfAppointments = array();
        $retVal = array();
        $i = 0;
        for($i = 0; $i < 12; $i++){
            $arrayMonths[] = $month;
            $arrayYear[] = $year;

            if($month < 10){
                $formattedDateInPast = $year.'-0'.$month.'-'.'00';
                $formattedDateInFuture = $year.'-0'.$month.'-'.'30';
            }else {
                $formattedDateInPast = $year.'-'.$month.'-'.'00';
                $formattedDateInFuture = $year.'-'.$month.'-'.'30';
            }
            $apps = Appointment::where('done', '=', '1')->where('clinic_id', $clinicAdmin->clinic_id)->where('approved', '=', '1')->whereDate('date','>=', $formattedDateInPast)->whereDate('date','<=', $formattedDateInFuture)->get();    
            $numberOfAppointments[] = count($apps);
            if($month - 1 == 0){
                $month = 12;
                $year--;
            }else {
                $month--;
            }
        }

        $i = 0;
        for($i = 0; $i < 12; $i++){
            switch($arrayMonths[$i]){
                case 1: 
                    $stringMonth = 'Jan';
                    break;
                case 2:
                    $stringMonth = 'Feb';
                    break;
                case 3: 
                    $stringMonth = 'Mar';
                    break;
                case 4: 
                    $stringMonth = 'Apr';
                    break;
                case 5: 
                    $stringMonth = 'May';
                    break;
                case 6: 
                    $stringMonth = 'Jue';
                    break;
                case 7: 
                    $stringMonth = 'Jul';
                    break;
                case 8: 
                    $stringMonth = 'Aug';
                    break;
                case 9: 
                    $stringMonth = 'Sept';
                    break;
                case 10: 
                    $stringMonth = 'Oct';
                    break;
                case 11: 
                    $stringMonth = 'Nov';
                    break;
                case 12: 
                    $stringMonth = 'Dec';    
                    break;
                default:
            }
            $object = new stdClass;
            $object->x = $stringMonth.' '.substr($arrayYear[$i],-2);
            $object->y = $numberOfAppointments[$i];
            $retVal[] = $object;
        }

        return $retVal;
    }
}