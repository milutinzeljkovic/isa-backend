<?php

use Illuminate\Database\Seeder;
use App\Doctor;
use App\Clinic;
use App\AppointmentType;
use App\Appointment;
use App\Price;
use App\OperationsRoom;


class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $appointmentToBeReserved = new Appointment();
        $appointmentToBeReserved->price = 1000;
        $appointmentToBeReserved->doctor_id = 1;
        $appointmentToBeReserved->clinic_id = 1;
        $appointmentToBeReserved->operations_room_id = 1;
        $appointmentToBeReserved->date = '2020-12-12 12:00:00';
        $appointmentToBeReserved->save();

        $appointmentToBeReserved = new Appointment();
        $appointmentToBeReserved->price = 1000;
        $appointmentToBeReserved->doctor_id = 1;
        $appointmentToBeReserved->clinic_id = 1;
        $appointmentToBeReserved->operations_room_id = 1;
        $appointmentToBeReserved->date = '2020-12-13 12:00:00';
        $appointmentToBeReserved->save();


        $appointmentToBeReserved = new Appointment();
        $appointmentToBeReserved->price = 1000;
        $appointmentToBeReserved->doctor_id = 1;
        $appointmentToBeReserved->clinic_id = 1;
        $appointmentToBeReserved->operations_room_id = 1;
        $appointmentToBeReserved->date = '2020-12-14 12:00:00';
        $appointmentToBeReserved->save();

        $appointmentToBeReserved = new Appointment();
        $appointmentToBeReserved->price = 1000;
        $appointmentToBeReserved->doctor_id = 1;
        $appointmentToBeReserved->clinic_id = 1;
        $appointmentToBeReserved->operations_room_id = 1;
        $appointmentToBeReserved->date = '2020-12-15 12:00:00';
        $appointmentToBeReserved->save();

        $appointmentToBeReserved = new Appointment();
        $appointmentToBeReserved->price = 1000;
        $appointmentToBeReserved->doctor_id = 2;
        $appointmentToBeReserved->clinic_id = 2;
        $appointmentToBeReserved->operations_room_id = 2;
        $appointmentToBeReserved->date = '2020-12-12 12:00:00';
        $appointmentToBeReserved->save();

        $price = new Price();
        $price->price = 1000;
        $price->clinic_id = 1;
        $price->appointment_type_id = 1;

        $price = new Price();
        $price->price = 1000;
        $price->clinic_id = 2;
        $price->appointment_type_id = 1;


    }
}
