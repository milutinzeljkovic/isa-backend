<?php

namespace App\Services;
use App\Services\IClinicService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

use App\Clinic;

class ClinicService implements IClinicService
{
    public function searchClinic()
    {
        $clinics = Redis::get('clinics');
        if($clinics == null)
        {
            $clinics = Clinic::all();
            Redis::set('clinics', $clinics);
            return $clinics;
        }
        else
            return $clinics;
    }

    public function addClinic($clinic)
    {
        $clinic = Clinic::create($clinic);
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