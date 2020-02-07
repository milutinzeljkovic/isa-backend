<?php

namespace App\Utils;
use App\Appointment;
use App\AppointmentType;
use App\Doctor;
use Carbon\Carbon;
use App\OperationsRoom;
use Illuminate\Support\Facades\DB;
use App\WorkingDay;
use App\Utils\IAddAppointmentStrategy;

class AddPredefinedAppointment implements IAddAppointmentStrategy
{
    public function addAppointment(Appointment $app)
    {
        $message = array('error' => false, 'message' => '');

        $doctor = Doctor::find($app->doctor_id);
        $user = $doctor->user()->first();
        $result = DB::table('appointment_type_doctor')
            ->where('doctor_id',$app->doctor_id)
            ->where('appointment_type_id', $app->appointment_type_id)
            ->get();
        
        if(count($result) == 0)
        {
            $doctor->appointmentTypes()->save($app->appointmentType()->first());
        }

        $apps = $user
            ->vacations()
            ->where('approved','=','1')
            ->where('from','<',$app->date)
            ->where('to','>',$app->date)
            ->get();
        if($apps->count() != 0)
        {
            $message['error'] = true;
            $message['message'] = 'Doctor is on vacation';
            return $message;
        }

        $doctorAppointments = $doctor
            ->appointments()
            ->where('date','=',$app->date)
            ->get();
        if($doctorAppointments->count() != 0)
        {
            $message['error'] = true;
            $message['message'] = 'Doctor is not free';
            return $message;
        }

        $doctorAppointments = $doctor
            ->appointments()
            ->where('date','>',Carbon::now())
            ->where('approved','=','1')
            ->get();

        
        $appointmentDate = Carbon::parse($app->date);
        $appointmentDateEnd = Carbon::parse($app->date);
        $appointmentDateEnd->addSeconds($app->duration*3600);

        foreach ($doctorAppointments as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($appointmentDate->greaterThanOrEqualTo($start) && $appointmentDate->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Appointment beginning is overlapping with doctors appointment';
            }
            if($appointmentDateEnd->greaterThanOrEqualTo($start) && $appointmentDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Appointment ending is overlapping';
            } 
            if($appointmentDate->lessThanOrEqualTo($start) && $appointmentDateEnd->greaterThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Overlaping';
            } 
        }

        $operationRoom = OperationsRoom::find($app->operations_room_id);
        $operationRoomAppointments = $operationRoom->appointments()->get();
        foreach ($operationRoomAppointments as $a) 
        {
            $start = Carbon::parse($a->date);
            $duration = $a->duration;
            $end = Carbon::parse($start);
            $end->addSeconds($duration*3600);
            if($appointmentDate->greaterThanOrEqualTo($start) && $appointmentDate->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room is not free!';
            }
            if($appointmentDateEnd->greaterThanOrEqualTo($start) && $appointmentDateEnd->lessThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Operating room is not free';
            }
            if($appointmentDate->lessThanOrEqualTo($start) && $appointmentDateEnd->greaterThanOrEqualTo($end))
            {
                $message['error'] = true;
                $message['message'] = 'Overlaping room';
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