<?php

namespace App\Utils;
use App\Appointment;

interface IAddAppointmentStrategy 
{
    public function addAppointment(Appointment $app);
}