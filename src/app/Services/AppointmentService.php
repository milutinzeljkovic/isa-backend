<?php

namespace App\Services;

use App\Services\IAppointmentService;
use App\Appointment;
use App\User;
use App\Clinic;
use App\Doctor;
use Illuminate\Support\Facades\DB;
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
        $app->operations_room_id = array_get($appointmentData, 'operations_room_id');

        $app->save();
       
        return response()->json(['created' => 'Appointment has been created'], 201);
    }

    public function reserve($appointment_id)
    {
        $user = Auth::user()->userable()->first();
        $appointment = Appointment::find($appointment_id);
        $clinic = Clinic::find($appointment->clinic_id);
        $userClinics = $user->clinics()->get();
        if($userClinics->contains('id',$clinic->id))
        {
            $user->clinics()->save($clinic);
        }
        $id = $user->id;

        DB::transaction(function () use($appointment, $id){

            DB::table('appointments')
                ->where('id', $appointment->id)
                ->where('lock_version', $appointment->lock_version)
                ->update(['patient_id' => $id]);
            DB::table('appointments')
                ->where('id', $appointment->id)
                ->update(['lock_version' => $appointment->lock_version +1]);
            DB::table('appointments')
                ->where('id', $appointment->id)
                ->update(['date' => $appointment->date]);
            
        });

        $updatedAppointment = Appointment::find($appointment_id);

        if($id != $updatedAppointment->patient_id)
        {
            //neko je vec rezervisao
        }
        else
        {
            return $updatedAppointment;
        }


    }

    public function showPatientHistory($id)
    {
        return Appointment::where('patient_id',$id)->where('done',1)->get();
    }
}