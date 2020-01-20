<?php

namespace App\Services;

use App\Services\IAppointmentService;
use App\Appointment;
use App\User;
use App\Clinic;
use App\Price;
use App\Doctor;
use Carbon\Carbon;
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

        $app->approved = 1;
        $app->appointment_type_id = array_get($appointmentData, 'app_type');
        $app->doctor_id = array_get($appointmentData, 'doctor');
        $app->operations_room_id = array_get($appointmentData, 'operations_room_id');
        $app->duration = array_get($appointmentData, 'duration');
        $clinic = $clinicAdmin->clinic()->first();
        $app->clinic()->associate($clinic);
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
            return response('Error',400);
        }
        else
        {
            return $updatedAppointment;
        }


    }

    function requestAppointment($id,$appointment)
    {
        $doctor = Doctor::find($id);
        $user = $doctor->user()->first();
        $app = $user
                ->vacations()
                ->where('from','<',array_get($appointment, 'date'))
                ->where('to','>',array_get($appointment, 'date'))
                ->get();
        if($app->count() != 0)
        {
            return response('Could not reserve appointment form a given date', 200);
        }
        $doctorAppointments = $doctor
                                ->appointments()
                                ->where('date','=',array_get($appointment, 'date'))
                                ->get();
        if($doctorAppointments->count() != 0)
        {
            return response('Doctor is not free', 200);
        }

        $doctorAppointments = $doctor->appointments()->get();

        $overlap = false;
        $appointmentDate = Carbon::parse(array_get($appointment, 'date'));
        foreach ($doctorAppointments as $a) {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($appointmentDate->greaterThanOrEqualTo($start) && $appointmentDate->lessThanOrEqualTo($end))
            {
                $overlap = true;
            }
        }

        if($overlap)
        {
            return response('Appointment overlapping', 200);
        }
        
        $price = Price::where('clinic_id','=',$doctor->clinic_id)
                        ->where('appointment_type_id','=',array_get($appointment, 'appointment_type'))
                        ->first();

        $app = new Appointment();
        $app->clinic_id = $doctor->clinic_id;
        $app->date = array_get($appointment, 'date');
        $app->price = $price->price;
        $app->done = 0;
        $app->appointment_type_id = array_get($appointment, 'appointment_type');
        $app->doctor_id = $doctor->id;
        $app->patient_id = Auth::user()->id;

        $app->save();

        return $app;
    }





    public function showPatientHistory($id)
    {
        if(Auth::user()->userable_type != "App\Patient")
        {
            return [];
        }
        return Appointment::with(['doctor' => function($q){
            $q->with('user');
        }])
            ->with(['appointmentType', 'operationsRoom', 'clinic'])
            ->where('patient_id',$id)->where('done',1)->get();

    }

}