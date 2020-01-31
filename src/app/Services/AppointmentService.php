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
use App\Utils\AppointmentAdding;
use App\Utils\AddPredefinedAppointment;
use App\Utils\AddCustomAppointment;


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

        $appointmentAdding = new AppointmentAdding();
        $message = $appointmentAdding->addAppointment(new AddPredefinedAppointment(), $app);

        if($message['error'] == false)
        {            
            $app->save();
            return response()->json(['created' => 'Appointment has been created'], 201);
        }
        else
        {
            return response($message['message'], 400);
        }

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
        $price = Price::where('clinic_id','=',$doctor->clinic_id)
            ->where('appointment_type_id','=',array_get($appointment, 'appointment_type'))
            ->first();

        $requestedAppointment = new Appointment();
        $requestedAppointment->doctor_id = $doctor->id;
        $requestedAppointment->date = array_get($appointment, 'date');
        $requestedAppointment->appointment_type_id = array_get($appointment, 'appointment_type');
        $requestedAppointment->patient_id = Auth::user()->id;
        $requestedAppointment->clinic_id = $doctor->clinic_id;
        $requestedAppointment->price = $price != null ? $price->price : 1000;
        $requestedAppointment->approved = 0;
        $requestedAppointment->done = 0;

        $appointmentAdding = new AppointmentAdding();
        $message = $appointmentAdding->addAppointment(new AddCustomAppointment(), $requestedAppointment);
        
        if($message['error'] == false)
        {
            $requestedAppointment->save();
            return $requestedAppointment;
        }
        else
        {
            return response($message['message'],200);
        }
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