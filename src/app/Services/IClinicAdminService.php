<?php

namespace App\Services;

interface IClinicAdminService
{
    function getAllDoctors();
    function getAllFacilities();
    function getAdminsClinic();
    function updateClinic(array $clinicData);
    function getOperations();
    function reserveOperation($operations_room_id, $operation_id);
    function reserveAppointmentRoom($operations_room_id, $appointment_id);
    function pendingAppointmentRequests();

    //function defineAvailableAppointment();
}