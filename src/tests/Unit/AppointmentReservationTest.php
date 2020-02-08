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
        $searchingApp = true;

        $clinics = Clinic::all();
        $appType = new AppointmentType();
        $appType->name='sadsa';
        $appType->save();

        $this->assertTrue($appType != null);


        $clinic = Clinic::with(['doctors' => function($q) use ($appType) {
            $q->with(['appointmentTypes'=> function($q) use ($appType){
                $q->where('name',$appType->name);
            }]);
        }])->first();

        $doctor;
        foreach($clinic->doctors as $d)
        {
            if($d->appointmentTypes->where('name',$appType->name) != [])
            {
                $doctor = $d;
                break;
            }
        }
        $this->assertTrue($doctor != null);

        $patient = Patient::first();

        $appointment = new Appointment();
        $appointment->date = Carbon::now()->addDays(14);
        $appointment->doctor_id = $doctor->id;
        $appointment->appointment_type_id = $appType->id;
        $appointment->clinic_id = $clinic->id;
        $appointment->patient_id = $patient->id;
        $appointment->save();




        $this->assertTrue($searchingApp);
    }
}
