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
        // $clinics = Clinic::all();
        // $appType = new AppointmentType();
        // $appType->name='sadsa';
        // $appType->save();

        // $this->assertTrue($appType != null);


        // $clinic = Clinic::with(['doctors' => function($q) use ($appType) {
        //     $q->with(['appointmentTypes'=> function($q) use ($appType){
        //         $q->where('name',$appType->name);
        //     }]);
        // }])->first();

        // $doctor;
        // foreach($clinic->doctors as $d)
        // {
        //     if($d->appointmentTypes->where('name',$appType->name) != [])
        //     {
        //         $doctor = $d;
        //         break;
        //     }
        // }
        // $this->assertTrue($doctor != null);
        
        $appointment = Appointment::where('patient_id',null)->first();
        $patient = Patient::first();
        $appointment->patient_id = $patient->id;
        $res = $appointment->save();




        $this->assertTrue($res);
    }
}
