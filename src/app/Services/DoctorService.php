<?php

namespace App\Services;
use App\Services\IDoctorService;
use App\Doctor;

class DoctorService implements IDoctorService
{
    function showDoctor($id)
    {
        return Doctor::find($id)->with('appointments')->first();
    }
}