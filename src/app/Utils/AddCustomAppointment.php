<?php

namespace App\Utils;
use App\Appointment;
use App\Doctor;
use Carbon\Carbon;
use App\OperationsRoom;
use App\Utils\IAddAppointmentStrategy;

class AddCustomAppointment implements IAddAppointmentStrategy
{
    public function addAppointment(Appointment $app)
    {
        $message = array('error' => false, 'message' => '');
        
        $doctor = $app->doctor;

        $user = $doctor->user()->first();
        //poklapanje sa godisnjnim odmorom
        $apps = $user
            ->vacations()
            ->where('approved','=','1')
            ->where('from','<',$app->date)
            ->where('to','>',$app->date)
            ->get();

        if($apps->count() != 0)
        {
            $message['message'] = 'Could not reserve appointment form a given date';
            $message['error'] = true;
        }

        //ako postoji pregled koji doktor treba da izvrsi a pocinje isto kad i zahtevani pregled
        $doctorAppointments = $doctor
            ->appointments()
            ->where('date','=',$app->date)
            ->get();

        if($doctorAppointments->count() != 0)
        {
            $message['message'] = 'Doctor is not free';
            $message['error'] = true;
        }

        $doctorAppointments = $doctor
            ->appointments()
            ->where('date','>',Carbon::now())
            ->where('approved','=','1')
            ->get();

        $appointmentDate = Carbon::parse($app->date);
        foreach ($doctorAppointments as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($appointmentDate->greaterThanOrEqualTo($start) && $appointmentDate->lessThanOrEqualTo($end))
            {
                $message['message'] = 'Appointment overlapping';
                $message['error'] = true;
            }
        }

        $c = new Carbon($app->date);
        $appointmentDayOfWeek = $c->dayOfWeek;
        $c = new Carbon($app->date);
        $pieces = explode(" ", $app->date);
        $appointmentHourOfDay = $pieces[1];

        $res = WorkingDay::where('doctor_id',$app->doctor_id)
            ->where('day','=',$appointmentDayOfWeek)
            ->where('from', '<', $appointmentHourOfDay)
            ->where('to', '>', $appointmentHourOfDay)
            ->get();
        if(count($res) == 0)
        {
            $res = WorkingDay::where('doctor_id',$app->doctor_id)
                ->where('day','=', $appointmentDayOfWeek)
                ->first();
            $message['error'] = true;
            $message['message'] = 'Work hours for given day: '.$res->from.' to '.$res->to;
        }


        return $message;

    }
}