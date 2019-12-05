<?php

namespace App\Services;

interface IClinicAdminService
{
    function getAllDoctors();
    function getAllFacilities();
    function getClinicDetails();
    //function defineAvailableAppointment();
}