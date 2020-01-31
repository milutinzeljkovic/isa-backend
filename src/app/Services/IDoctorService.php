<?php

namespace App\Services;

interface IDoctorService 
{
    function showDoctor($id);
    function seeIfDoctorUsed($id);
    function getWorkingHours($id);
}