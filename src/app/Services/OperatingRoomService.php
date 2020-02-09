<?php

namespace App\Services;

use App\Services\IOperatingRoomService;
use App\OperationsRoom;
use App\Clinic;
use App\Operations;
use App\Appointment;
use Auth;
use Carbon\Carbon;
use stdClass;
use App\Utils\SimpleFactory;

class OperatingRoomService implements IOperatingRoomService
{
    public function addOperatingRoom(array $operatingRoomData)
    {
        $factory = new SimpleFactory();
        $user = Auth::user();
        $clinicAdmin = $user->userable()->first();

        $opRoom = $factory->createOperatingRoom();

        $opRoom->name = array_get($operatingRoomData, 'name');
        $opRoom->number = array_get($operatingRoomData, 'number');
        $opRoom->clinic_id = $clinicAdmin->clinic_id;
        $opRoom->reserved = 0;
        $opRoom->save();
       

        return response()->json(['created' => 'Operating room has been created'], 201);
    }

    public function getOperatingRooms()
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->first();
        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->first();
        $facilities = $clinic->operationRooms;

        return $facilities;
    }

    public function seeIfOpRoomBooked($id)
    {

        $allApps = Appointment::where('date','>',Carbon::now())
            ->where('done',0)
            ->get();
        foreach($allApps as $appointment)
        {
            if($appointment->operations_room_id != null)
            {
                if($appointment->operations_room_id == $id)
                {   
                    return response()->json(["true"], 200);
                }
            }
        }

        return response()->json(["false"], 200);
    }

    function searchOperatingRooms($name, $number, $date)
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->first();
        $list = collect();
        if($date == "null"){
            $date = null;
        }


        $query = OperationsRoom::query();
        $query->where('clinic_id',$clinicAdmin->clinic_id);

        $query->where('name', 'like', '%'.$name.'%');
        if($number != null){
            $query->where('number', '=', $number);
        }

        $found = false;
        if($date != null){
            $month = explode('-', $date)[1];
            $day = explode('-', $date)[2];

            if(strlen($month) == 1){
                $month = '0'.$month;
            }

            if(strlen($day) == 1){
                $day = '0'.$day;
            }

            $datee = explode('-', $date)[0].'-'.$month.'-'.$day;  //formated date

            $operatingRooms = $query->get();
            foreach($operatingRooms as $opRoom){
                $found = false;
                $operations = Operations::where('operations_rooms_id', $opRoom->id)->whereDate('date', '>=', Carbon::now())->get();
                $appointments = Appointment::where('operations_room_id', $opRoom->id)->whereDate('date', '>=', Carbon::now())->get();
                if(count($appointments) > 0 || count($operations) > 0){
                    foreach($appointments as $appointment){
                        $date1 = explode(' ', $appointment->date)[0];
                        if($date1 == $datee){
                            $found = true;
                        }
                    }

                    foreach($operations as $operation){
                        $date2 = explode(' ', $operation->date)[0];
                        if($date2 == $datee){
                            $found = true;
                        }
                    }
                    
                    if($found == false){
                        $list->push($opRoom);
                    }
                }else {
                    $list->push($opRoom);
                }
            }
        }else {
            $list = $query->get();
        }
        return $list;
    }

    public function getAppointments($id)
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->first();

        $operatingRoom = OperationsRoom::where('id', $id)->get()[0];
        $appointments = Appointment::where('operations_room_id', $operatingRoom->id)->where('clinic_id', $clinicAdmin->clinic_id)->where('approved', '=', 1)->where('patient_id', '!=', null)->with('appointmentType')
        ->with(['patient' => function($q) {
            $q->with('user');
        }])->get();

        return $appointments;
    }

    public function getFirstFreeDate($id)
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->first();

        $operatingRoom = OperationsRoom::where('id', $id)->get()[0];

        $appointments = Appointment::where('operations_room_id', $operatingRoom->id)->whereDate('date', '>=', Carbon::now())->where('approved', '=', 1)->where('patient_id', '!=', null)->where('clinic_id', $clinicAdmin->clinic_id)->get();
        $operations = Operations::where('operations_rooms_id', $operatingRoom->id)->whereDate('date', '>=', Carbon::now())->where('patient_id', '!=', null)->where('clinic_id', $clinicAdmin->clinic_id)->get();

        $year = (int)explode('-', date("Y-m-d"))[0];
        $month = (int)explode('-', date("Y-m-d"))[1];
        $day = (int)explode('-', date("Y-m-d"))[2];
        $day = $day + 1;

        if(count($appointments) == 0 && count($operations) == 0){
            return $year.'-'.$month.'-'.$day;
        }

        $dates = array();
        foreach($appointments as $appointment){
           $dates[] = explode(' ', $appointment->date)[0];
        }

        $datesOperations = array();
        foreach($operations as $operation){
           $datesOperations[] = explode(' ', $operation->date)[0];
        }

        $prestupna = false;

        if($year % 4 == 0){
            $prestupna = true;
        }

        while($month <= 12){
            if($month % 2 == 0){
                if($month == 2){
                    if($prestupna){  //ako je februar
                        while($day <= 29){
                            if($day < 10){
                                if(!in_array(($year.'-0'.$month.'-0'.$day),$dates) && !in_array(($year.'-0'.$month.'-0'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }else{
                                if(!in_array(($year.'-'.$month.'-'.$day),$dates) && !in_array(($year.'-0'.$month.'-'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }
                            $day++;
                        }
                    }else {
                        while($day <= 28){
                            if($day < 10){
                                if(!in_array(($year.'-0'.$month.'-0'.$day),$dates) && !in_array(($year.'-0'.$month.'-0'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }else{
                                if(!in_array(($year.'-'.$month.'-'.$day),$dates) && !in_array(($year.'-'.$month.'-'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }
                            $day++;
                        }
                    }
                }else{
                    while($day <= 30){
                        if($day < 10){
                            if($month < 10){
                                if(!in_array(($year.'-0'.$month.'-0'.$day),$dates) && !in_array(($year.'-0'.$month.'-0'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }else {
                                if(!in_array(($year.'-'.$month.'-0'.$day),$dates) && !in_array(($year.'-'.$month.'-0'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }
                        }else{
                            if($month < 10){
                                if(!in_array(($year.'-0'.$month.'-'.$day),$dates) && !in_array(($year.'-0'.$month.'-'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }else {
                                if(!in_array(($year.'-'.$month.'-'.$day),$dates) && !in_array(($year.'-'.$month.'-'.$day),$datesOperations)){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }
                        }
                        $day++;
                    }
                }
            }else {
                while($day <= 31){
                    if($day < 10){
                        if($month < 10){
                            if(!in_array(($year.'-0'.$month.'-0'.$day),$dates) && !in_array(($year.'-0'.$month.'-0'.$day),$datesOperations)){
                                return $year.'-'.$month.'-'.$day;
                            }
                        }else {
                            if(!in_array(($year.'-'.$month.'-0'.$day),$dates) && !in_array(($year.'-'.$month.'-0'.$day),$datesOperations)){
                                return $year.'-'.$month.'-'.$day;
                            }
                        }
                    }else{
                        if($month < 10){
                            if(!in_array(($year.'-0'.$month.'-'.$day),$dates) && !in_array(($year.'-0'.$month.'-'.$day),$datesOperations)){
                                return $year.'-'.$month.'-'.$day;
                            }
                        }else {
                            if(!in_array(($year.'-'.$month.'-'.$day),$dates) && !in_array(($year.'-'.$month.'-'.$day),$datesOperations)){
                                return $year.'-'.$month.'-'.$day;
                            }
                        }
                    }
                    $day++;
                }
            }

            //za iteriranje
            $day = 1;
            if($month + 1 > 12){
                $month = 1;
                $year++;
            }else {
                $month++;
            }
        }
    }
}