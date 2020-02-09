<?php

namespace App\Utils;
use App\AppointmentType;
use App\WorkingDay;
use App\Doctor;
use App\Nurse;
use App\OperationsRoom;

class SimpleFactory 
{
    public function createAppointmentType()
    {
        return new AppointmentType();
    }

    public function createWorkingDay()
    {
        return new WorkingDay();
    }

    public function createOperatingRoom()
    {
        return new OperationsRoom();
    }

    public function createNurse()
    {
        return new Nurse();
    }

    public function createDoctor()
    {
        return new Doctor();
    }
}