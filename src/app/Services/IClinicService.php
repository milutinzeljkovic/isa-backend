<?php

namespace App\Services;

interface IClinicService 
{
    function searchClinic($name);
    function addClinic($clinic);
    function deleteClinic($clinic);
    function updateClinic($clinic,$values);
}