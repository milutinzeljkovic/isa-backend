<?php

namespace App\Services;

use App\Services\IAppointmentService;
use App\Appointment;
use Auth;

class AppointmentService implements IAppointmentService
{
    public function addAppointment(array $appointmentData)
    {
        $user = Auth::user();
        $clinicAdmin = $user->userable()->get()[0];
        $app = new Appointment();

        $app->clinic_id = $clinicAdmin->clinic_id;
        $app->date = array_get($appointmentData, 'date');
        $app->price = array_get($appointmentData, 'price');
        $app->done = 0;
        $app->appointment_type_id = array_get($appointmentData, 'app_type');
        $app->doctor_id = array_get($appointmentData, 'doctor');
        $app->patient_id = 0;
        $app->operation_rooms_id = 0;

        $app->save();
       

        return response()->json(['created' => 'Appointment has been created'], 201);
    }
}