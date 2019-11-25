<?php

namespace App\Services;

interface IClinicService 
{
    function addClinic($clinic);
    function deleteClinic($clinic);
    function updateClinic($clinic,$values);
}