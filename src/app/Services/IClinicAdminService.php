<?php

namespace App\Services;

interface IClinicAdminService
{
    function getAllDoctors();
    function getAllFacilities();
    function getAdminsClinic();
    function updateClinic(array $clinicData);
    function getOperations();
    function editOperation(array $userData);
    //function defineAvailableAppointment();
}