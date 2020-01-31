<?php

namespace App\Services;

interface IAppointmentTypeService
{
    function addAppointmentType(array $appTypeData);
    function seeIfAppTypeUsed($id);
}