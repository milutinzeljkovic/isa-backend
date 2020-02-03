<?php

namespace App\Utils;

use App\Appointment;
use Utils\IAddAppointmentStrategy;

class AppointmentAdding
{
    public function addAppointment($strategy, Appointment $appointment)
    {
        return $strategy->addAppointment($appointment);
    }
}