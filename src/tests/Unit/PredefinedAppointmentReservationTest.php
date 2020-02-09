<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Reshadman\OptimisticLocking\StaleModelLockingException;
use Illuminate\Support\Facades\DB;

use App\Appointment;
use App\AppointmentType;
use App\Clinic;
use Carbon\Carbon;
use App\Patient;
use App\Price;
use App\User;
use App\ClinicalCenter;
use App\Doctor;
use App\OperationsRoom;



class PredefinedAppointmentReservationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        $patient = Patient::all()[0];

        $appointment = Appointment::where('id',1)->first();
        $appointment->patient_id=$patient->id;
        $appointment->save();

        $this->assertEquals($appointment->patient_id, $patient->id);

    }



}


