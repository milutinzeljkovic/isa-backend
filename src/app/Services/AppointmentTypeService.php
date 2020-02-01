<?php

namespace App\Services;

use App\Services\IAppointmentTypeService;
use App\AppointmentType;
use App\Clinic;
use Auth;

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

}