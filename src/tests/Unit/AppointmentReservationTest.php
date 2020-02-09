<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;
use App\Appointment;
use App\AppointmentType;
use App\Clinic;
use Carbon\Carbon;
use App\Patient;

class AppointmentReservationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        
        $appointment = Appointment::where('patient_id',null)->first();
        $patient = Patient::first();
        $appointment->patient_id = $patient->id;
        $res = $appointment->save();




        $this->assertTrue($res);
    }
}
