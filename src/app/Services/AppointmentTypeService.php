<?php

namespace App\Services;

use App\Services\IAppointmentTypeService;
use App\AppointmentType;

class AppointmentTypeService implements IAppointmentTypeService
{
    public function addAppointmentType(array $appTypeData)
    {
        $appType = new AppointmentType();

        $appType->name = array_get($appTypeData, 'name');
        $appType->save();
       

        return response()->json(['created' => 'Appointment type has been created'], 201);
    }

    public function getAppointmentTypes()
    {
        $appointments = AppointmentType::all();
        return $appointments;
    }

}