<?php

namespace App\Services;

use App\Services\IOperatingRoomService;
use App\OperationsRoom;
use App\Clinic;
use Auth;

class OperatingRoomService implements IOperatingRoomService
{
    public function addOperatingRoom(array $operatingRoomData)
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $opRoom = new OperationsRoom();

        $opRoom->name = array_get($operatingRoomData, 'name');
        $opRoom->number = array_get($operatingRoomData, 'number');
        $opRoom->clinic_id = $clinicAdmin->clinic_id;
        $opRoom->reserved = 0; 
        $opRoom->appointment_id = 0;
        $opRoom->save();
       

        return response()->json(['created' => 'Operating room has been created'], 201);
    }

    public function getOperatingRooms(){
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];

        $clinic = Clinic::where('id', $clinicAdmin->clinic_id)->get()[0];
        $facilities = $clinic->operationRooms;

        return $facilities;
    }

}