<?php

namespace App\Services;

use App\Services\IAppointmentService;
use App\Appointment;
use App\OperationsRoom;
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
        if(array_get($appointmentData, 'discount'))
        {
            $app->discount = array_get($appointmentData, 'discount');
        }
        if(array_get($appointmentData, 'duration'))
        {
            $app->duration = array_get($appointmentData, 'duration');
        }
        else
        {
            $app->duration = 1;
        }
        $app->done = 0;
        $app->approved = 1;
        $app->appointment_type_id = array_get($appointmentData, 'app_type');
        $app->doctor_id = array_get($appointmentData, 'doctor');
        $app->operations_room_id = array_get($appointmentData, 'operations_room_id');
        $clinic = $clinicAdmin->clinic()->first();
        $app->clinic()->associate($clinic);


        $doctor = Doctor::find($app->doctor_id);
        $user = $doctor->user()->first();
        $apps = $user
                ->vacations()
                ->where('approved','=','1')
                ->where('from','<',array_get($appointmentData, 'date'))
                ->where('to','>',array_get($appointmentData, 'date'))
                ->get();
        if($apps->count() != 0)
        {
            return response('Could not create appointment form a given date', 400);
        }


        $doctorAppointments = $doctor
            ->appointments()
            ->where('date','=',array_get($appointmentData, 'date'))
            ->get();
        if($doctorAppointments->count() != 0)
        {
            return response('Doctor is not free', 400);
        }

        $doctorAppointments = $doctor
        ->appointments()
        ->where('date','>',Carbon::now())
        ->where('approved','=','1')
        ->get();

        $overlap = false;
        $appointmentDate = Carbon::parse(array_get($appointmentData, 'date'));
        $appointmentDateEnd = Carbon::parse(array_get($appointmentData, 'date'));
        $appointmentDateEnd->addSeconds(array_get($appointmentData, 'duration') * 3600);

        foreach ($doctorAppointments as $a) {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($appointmentDate->greaterThanOrEqualTo($start) && $appointmentDate->lessThanOrEqualTo($end))
            {
                $overlap = true;
            }
            if($appointmentDateEnd->greaterThanOrEqualTo($start) && $appointmentDateEnd->lessThanOrEqualTo($end))
            {
                $overlap = true;
            }

            
        }


        if($overlap)
        {
            return response('Appointment overlapping', 400);
        }


        $operationRoom = OperationsRoom::find($app->operations_room_id);
        $operationRoomOverlap = false;
        $operationRoomAppointments = $operationRoom->appointments()->get();
        foreach ($operationRoomAppointments as $a) {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($appointmentDate->greaterThanOrEqualTo($start) && $appointmentDate->lessThanOrEqualTo($end))
            {
                $operationRoomOverlap = true;
            }
            if($appointmentDateEnd->greaterThanOrEqualTo($start) && $appointmentDateEnd->lessThanOrEqualTo($end))
            {
                $operationRoomOverlap = true;
            }
        }

        if($operationRoomOverlap)
        {
            return response('Operation room overlapping', 400);
        }

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

    //id doktora i pregled koji pacijent oce da rezervise
    function requestAppointment($id,$appointment)
    {
        if(Carbon::parse(array_get($appointment, 'date')) < Carbon::now() || array_get($appointment,'appointment_type') == null)
        {
            return response('Bad request', 400);
        }

        $doctor = Doctor::find($id);
        $user = $doctor->user()->first();
        //poklapanje sa godisnjnim odmorom
        $app = $user
                ->vacations()
                ->where('approved','=','1')
                ->where('from','<',array_get($appointment, 'date'))
                ->where('to','>',array_get($appointment, 'date'))
                ->get();
        if($app->count() != 0)
        {
            return response('Could not reserve appointment form a given date', 200);
        }

        //ako postoji pregled koji doktor treba da izvrsi a pocinje isto kad i zahtevani pregled
        $doctorAppointments = $doctor
            ->appointments()
            ->where('date','=',array_get($appointment, 'date'))
            ->get();
        if($doctorAppointments->count() != 0)
        {
            return response('Doctor is not free', 200);
        }
        

        $doctorAppointments = $doctor
            ->appointments()
            ->where('date','>',Carbon::now())
            ->where('approved','=','1')
            ->get();

        //provera preklapanja pocetka zahtevanog pregelda sa pregledom koji doktor treba da odrzi
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
        
        //default cena za taj pregled
        $price = Price::where('clinic_id','=',$doctor->clinic_id)
            ->where('appointment_type_id','=',array_get($appointment, 'appointment_type'))
            ->first();

        $app = new Appointment();
        $app->clinic_id = $doctor->clinic_id;
        $app->date = array_get($appointment, 'date');
        $app->price = $price->price;
        //pending approval
        $app->approved = 0;
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

    function searchAppointment($date, $type)
    {
        $searchByTyoe = ($type == null ? false : true);
        $searchByDate = ($date == null ? false : true);

        $query = Appointment::query();
        if($type != null)
        {
            $query->where('appointment_type_id',$type);
        }
        if($date != null)
        {
            $query->whereDate('date', $date);

        }
        else
        {
            $query->where('date','>',Carbon::now());
        }
        $query->where('patient_id', null);

        $query->with(['doctor' => function ($q) {
            $q->with('user');
        }]);

        $query->with('operationsRoom');
        $query->with('appointmentType');
        $query->with('clinic');

        return $query->get();

    }


}