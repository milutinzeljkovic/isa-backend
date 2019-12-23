<?php

namespace App\Services;
use App\Services\IClinicService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


use App\Clinic;

class ClinicService implements IClinicService
{
    public function searchClinic($name, $date, $stars, $address, $appointmentType)
    {

        $searchByName = ($name == null ? false : true);
        $searchByDate = ($date == null ? false : true);
        $searchByStars = ($stars == null ? false : true);
        $searchByAddress = ($address == null ? false : true);
        $searchByType = ($appointmentType == null ? false : true);
        $results = DB::table('clinics')
            ->when($searchByDate, function($query) use ($date, $searchByDate){
                $query->join('appointments', function ($join) use ($searchByDate, $date){
                    $join->on('clinics.id', '=', 'appointments.clinic_id')
                    ->when($searchByDate, function($query) use ($date){
                        return $query->whereDate('date', '=', $date);
                    });
                });
            })
            ->when($searchByType, function($query) use ($appointmentType, $searchByType){
                $query->join('appointment_type_clinic', function ($join) use ($searchByType, $appointmentType){
                    $join->on('clinics.id', '=', 'appointment_type_clinic.clinic_id')
                    ->when($searchByType, function($query) use ($appointmentType){
                        return $query->where('appointment_type_clinic.appointment_type_id', '=', $appointmentType);
                    });
                });
            })
            ->when($searchByName, function($query) use ($name){
                return $query->where('name', 'like', '%'.$name.'%');
            })
            ->when($searchByStars, function($query) use ($stars){
                return $query->where('stars_count', '=', $stars);
            })
            ->when($searchByAddress, function($query) use ($address){
                return $query->where('address', 'like', '%'.$address.'%');
            })
            ->select('clinics.id','clinics.name','clinics.address','clinics.stars_count', 'clinics.lat', 'clinics.lng')
            ->groupBy('clinics.name')
            ->get();

        return Clinic::hydrate( $results->toArray() );
    }

    public function showClinic($id)
    {
        return Clinic::with('appointmentTypes')->find($id);
    }

    public function addClinic($clinic)
    {
        $currentUser = Auth::user();
        $clinicalCenterAdmin = $currentUser->userable()->get()[0];
        $clinic = Clinic::create($clinic);
        $clinic->clinical_center_id = $clinicalCenterAdmin->clinical_center_id;
        $clinic->save();
        $clinics = Clinic::all();
        Redis::set('clinics',$clinics);

        return $clinic;
    }
    public function deleteClinic($clinic)
    {

    }
    public function updateClinic($clinic,$values)
    {

    }



}