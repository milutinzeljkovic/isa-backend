<?php

namespace App\Services;

use App\Services\IAppointmentService;
use App\Appointment;
use App\User;
use App\Doctor;
use Auth;
use DateTime;

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
        $user = User::where('id',array_get($appointmentData,'doctor'))->get()[0];
        $doctor = $user->userable()->get()[0];
        $app->doctor_id = $doctor->id;
        $app->operations_rooms_id = array_get($appointmentData, 'operations_rooms_id');

        $app->save();
       
        return response()->json(['created' => 'Appointment has been created'], 201);
    }
}