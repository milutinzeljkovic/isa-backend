<?php

namespace App\Services;

interface IClinicService 
{
    function searchClinic($name, $date, $stars, $address, $appointmentType);
    function addClinic($clinic);
    function showClinic($id);
    function deleteClinic($clinic);
    function updateClinic($clinic,$values);
}