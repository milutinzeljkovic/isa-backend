<?php

namespace App\Services;

interface IDoctorService 
{
    function showDoctor($id);
    function searchDoctors($clinic_id, $name, $date, $stars, $appointmentType);
    function showDoctorsAppointments($id);
}