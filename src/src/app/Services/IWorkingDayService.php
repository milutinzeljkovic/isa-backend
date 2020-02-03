<?php

namespace App\Services;

interface IWorkingDayService
{
    function getDoctorsWorkingHours($id);
    function updateDoctorsWorkingHours($id, array $data);
}