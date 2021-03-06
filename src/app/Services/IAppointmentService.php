<?php

namespace App\Services;


interface IAppointmentService
{
    function addAppointment(array $appointmentData);
    function reserve($appointment_id);
    function showPatientHistory($id);
    function requestAppointment($id,$appointment);
    function searchAppointment($date, $type);
}