<?php

namespace App\Services;

use App\Services\IOperatingRoomService;
use App\OperationsRoom;
use App\Clinic;
use App\Appointment;
use Auth;
use Carbon\Carbon;

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
}