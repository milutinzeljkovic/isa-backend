<?php

namespace App\Services;

interface IDoctorService 
{
    function showDoctor($id);
    function searchDoctors($clinic_id, $name, $date, $stars, $appointmentType);
    function showDoctorsAppointments($id);
    function getApointments();
    function medicalReportForAppointment(array $userData);
    function getDataForDoctor($appointment_id);
    function sheduleAnOperation(array $userData);
    function getOperations();

    function seeIfDoctorUsed($id);
    function getWorkingHours($id);
    function getDoctorsAppointmentTypes();
}