<?php

namespace App\Services;

use App\Services\IAppointmentTypeService;
use App\AppointmentType;
use App\Clinic;
use Auth;
use App\Appointment;

class AppointmentTypeService implements IAppointmentTypeService
{
    public function addAppointmentType(array $appTypeData)
    {
        $appType = new AppointmentType();

        $appType->name = array_get($appTypeData, 'name');
        $user = Auth::user()->userable()->first();

        $clinic = Clinic::find($user->clinic_id);
        $clinic->appointmentTypes()->save($appType, ['price' => array_get($appTypeData, 'price') ]);

        $appType->save();
       

        return response()->json(['created' => 'Appointment type has been created'], 201);
    }

    public function getAppointmentTypes()
    {
        $appointments = AppointmentType::all();
        return $appointments;
    }

    public function appointmentTypesClinic()
    {
        $user = Auth::user();
        $admin = $user->userable()->first();
        $clinic = $admin->clinic()->first();
        return $clinic->appointmentTypes()->get();
    }

    public function seeIfAppTypeUsed($id)
    {
        $allApps = Appointment::all();

        foreach($allApps as $appointment){
            if($appointment->appointment_type_id == $id){    //za sad ne proverava da li je termin zakazan
                return response()->json(["true"], 200);
            }
        }

        return response()->json(["false"], 200);
    }
}