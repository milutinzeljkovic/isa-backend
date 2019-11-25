<?php

namespace App\Services;
use App\Services\IClinicService;
use Illuminate\Support\Facades\Auth;
use App\Clinic;

class ClinicService implements IClinicService
{
    public function searchClinic()
    {
        return Clinic::all();
    }

    public function addClinic($clinic)
    {
        $clinic = Clinic::create($clinic);
        return $clinic;
    }
    public function deleteClinic($clinic)
    {

    }
    public function updateClinic($clinic,$values)
    {

    }


}