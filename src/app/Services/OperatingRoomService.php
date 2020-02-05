<?php

namespace App\Services;

use App\Services\IOperatingRoomService;
use App\OperationsRoom;
use App\Clinic;
use App\Appointment;
use Auth;
use Carbon\Carbon;
use stdClass;

class OperatingRoomService implements IOperatingRoomService
{
    public function addOperatingRoom(array $operatingRoomData)
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->first();

        $opRoom = new OperationsRoom();

        $opRoom->name = array_get($operatingRoomData, 'name');
        $opRoom->number = array_get($operatingRoomData, 'number');
        $opRoom->clinic_id = $clinicAdmin->clinic_id;
        $opRoom->reserved = 0;
        $opRoom->save();
       

        return response()->json(['created' => 'Operating room has been created'], 201);
    }

    public function getOperatingRooms(){
        $user = Auth::user();
        $clinicAdmin = $user->userable()->first();

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->first();
        $facilities = $clinic->operationRooms;

        return $facilities;
    }

    public function seeIfOpRoomBooked($id){
        $allApps = Appointment::all();

        foreach($allApps as $appointment){
            if($appointment->operations_room_id != null){
                if($appointment->operations_room_id == $id){    //za sad ne proverava da li je termin zakazan
                    return response()->json(["true"], 200);
                }
            }
        }

        return response()->json(["false"], 200);
    }

    function searchOperatingRooms($name, $number, $date)
    {
        $list = collect();
        if($date == "null"){
            $date = null;
        }

        $query = OperationsRoom::query();
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
                $appointments = Appointment::where('operations_room_id', $opRoom->id)->whereDate('date', '>=', Carbon::now())->get();
                if(count($appointments) > 0){
                    foreach($appointments as $appointment){
                        $date1 = explode(' ', $appointment->date)[0];
                        if($date1 == $datee){
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

        $year = (int)explode('-', date("Y-m-d"))[0];
        $month = (int)explode('-', date("Y-m-d"))[1];
        $day = (int)explode('-', date("Y-m-d"))[2];
        $day = $day + 1;

        if(count($appointments) == 0){
            return $year.'-'.$month.'-'.$day;
        }

        $dates = array();
        foreach($appointments as $appointment){
           $dates[] = explode(' ', $appointment->date)[0];
        }

        $prestupna = false;

        if($month == 2){
            while($day <= 29){
                if($day < 10){
                    if(!in_array(($year.'-0'.$month.'-0'.$day),$dates)){
                        return $year.'-'.$month.'-'.$day;
                    }
                }else{
                    if(!in_array(($year.'-'.$month.'-'.$day),$dates)){
                        return $year.'-'.$month.'-'.$day;
                    }
                }
                $day++;
            }
        }
                
        
            /*if($month % 2 == 0){
                if($prestupna){
                    if($month == 2){
                        while($day <= 29){
                            foreach($sortedDates as $sortedDate){
                                $yearSort = (int)explode('-', $sortedDate->date)[0];
                                $monthSort = (int)explode('-', $sortedDate->date)[1];
                                $daySort = (int)explode('-', $sortedDate->date)[2];
                    
                                if($year > $yearSort){
                                    continue;
                                }

                                if($year == $yearSort && $month > $monthSort){
                                    continue;
                                }

                                if($year == $yearSort && $month == $monthSort && $day > $daySort){
                                    continue;
                                }

                                if($year == $yearSort && $month == $monthSort && $day == $daySort){
                                    break;
                                }

                                if($year != $yearSort || $month != $monthSort || $day != $daySort){
                                    return $year.'-'.$month.'-'.$day;
                                }
                            }
                            $day = $day + 1;
                        }
                        $day = 1;
                        if(($month + 1) > 12){
                            $month = 1;
                            $year = $year + 1;
                        }else {
                            $month = $month + 1;
                        }
                        return "Jeca";
                    }else {
                        //svaki paran mesec a da nije februar
                    }
                }else {
                    while($day <= 30){
                        foreach($sortedDates as $sortedDate){
                            $yearSort = (int)explode('-', $sortedDate->date)[0];
                            $monthSort = (int)explode('-', $sortedDate->date)[1];
                            $daySort = (int)explode('-', $sortedDate->date)[2];
                
                            if($year > $yearSort){
                                continue;
                            }

                            if($year == $yearSort && $month > $monthSort){
                                continue;
                            }

                            if($year == $yearSort && $month == $monthSort && $day > $daySort){
                                continue;
                            }

                            if($year == $yearSort && $month == $monthSort && $day == $daySort){
                                break;
                            }

                            if($year != $yearSort || $month != $monthSort || $day != $daySort){
                                return $year.'-'.$month.'-'.$day;
                            }
                        }
                        $day = $day + 1;
                    }
                    $day = 1;
                    if(($month + 1) > 12){
                        $month = 1;
                        $year = $year + 1;
                    }else {
                        $month = $month + 1;
                    }
                }
            }else {
                while($day <= 31){
                    foreach($sortedDates as $sortedDate){
                        $yearSort = (int)explode('-', $sortedDate->date)[0];
                        $monthSort = (int)explode('-', $sortedDate->date)[1];
                        $daySort = (int)explode('-', $sortedDate->date)[2];
            
                        if($year > $yearSort){
                            continue;
                        }

                        if($year == $yearSort && $month > $monthSort){
                            continue;
                        }

                        if($year == $yearSort && $month == $monthSort && $day > $daySort){
                            continue;
                        }

                        if($year == $yearSort && $month == $monthSort && $day == $daySort){
                            break;
                        }

                        if($year != $yearSort || $month != $monthSort || $day != $daySort){
                            return $year.'-'.$month.'-'.$day;
                        }
                    }
                    $day = $day + 1;
                }
                $day = 1;
                if(($month + 1) > 12){
                    $month = 1;
                    $year = $year + 1;
                }else {
                    $month = $month + 1;
                }
            }*/
    }
}