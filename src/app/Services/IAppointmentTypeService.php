<?php

namespace App\Services;

interface IAppointmentTypeService
{
    function addAppointmentType(array $appTypeData);
    function getAppointmentTypes();
    function appointmentTypesClinic();
    function seeIfAppTypeUsed($id);
}