<?php

namespace App\Services;

use App\Services\IOperatingRoomService;
use App\OperationsRoom;
use App\Clinic;
use App\Appointment;
use Auth;

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
}