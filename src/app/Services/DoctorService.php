<?php

namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Services\IDoctorService;
use App\Doctor;

class DoctorService implements IDoctorService
{
    function showDoctor($id)
    {
        return Doctor::find($id)->with('appointments')->first();
    }

    function showDoctorsAppointments($id)
    {
        $result = Doctor::with('user')->with(['appointments' => function ($q) {
            $q->where('patient_id','=',null)
              ->where('date', '>', Carbon::createFromTimestamp(Carbon::now()->getTimestamp())->toDateTimeString())
                ->with('operationsRoom')
                ->with('appointmentType')
                ->with('doctor.user');
        }])
        ->with('appointmentTypes')
        ->where('id',$id)
        ->first();

        return $result;
    }

    function searchDoctors($clinic_id, $name, $date, $stars, $appointmentType)
    {
        $searchByName = ($name == null ? false : true);
        $searchByDate = ($date == null ? false : true);
        $searchByStars = ($stars == null ? false : true);
        $searchByType = ($appointmentType == null ? false : true);
        $results = DB::table('doctors')
            ->where('doctors.clinic_id', $clinic_id)
            ->when($searchByDate, function($query) use ($date, $searchByDate, $searchByType, $appointmentType){
                $query->join('appointments', function ($join) use ($searchByDate, $date, $searchByType, $appointmentType){
                    $join->on('doctors.id', '=', 'appointments.doctor_id')
                    ->when($searchByDate, function($query) use ($date, $searchByType, $appointmentType){
                        $query
                            ->whereDate('appointments.date', '=', $date)
                            ->when($searchByType, function($query) use ($appointmentType){
                                $query->where('appointments.appointment_type_id', '=', $appointmentType);
                            });
                            return $query;
                    });
                });
            })
            ->when($searchByType, function($query) use ($appointmentType, $searchByType){
                $query->join('appointment_type_doctor', function ($join) use ($searchByType, $appointmentType){
                    $join->on('doctors.id', '=', 'appointment_type_doctor.doctor_id')
                    ->when($searchByType, function($query) use ($appointmentType){
                        return $query->where('appointment_type_doctor.appointment_type_id', '=', $appointmentType);
                    });
                });
            })
            ->when(true, function($query) use ($name, $searchByName){
                $query->join('users', function ($join) use ($searchByName, $name){
                    $join->on('doctors.id', '=', 'users.userable_id')
                    ->where('users.userable_type' ,'=', 'App\\Doctor')
                    ->when($searchByName, function($query) use ($name){
                        return $query->where('users.name', 'like', '%'.$name.'%');
                    });
                });
            })
            ->when($searchByStars, function($query) use ($stars){
                return $query->where('stars_count', '=', $stars);
            })
            ->select('doctors.id','users.name','users.email','users.last_name','doctors.stars_count')
            ->groupBy('doctors.id')
            ->get();

            return $results;
    }

}