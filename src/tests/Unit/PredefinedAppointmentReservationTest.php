<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Reshadman\OptimisticLocking\StaleModelLockingException;
use Illuminate\Support\Facades\DB;

use App\Appointment;
use App\User;
use App\Patient;



class PredefinedAppointmentReservationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        $patient = Patient::first();


        $appointment = Appointment::where('id',1)->first();
        $this->updateAppointment($appointment, $patient->id);
        $this->assertEquals($patient->id, $appointment->patient_id);

    }


    public static function updateAppointment($appointment, $id)
    {
        DB::transaction(function () use($appointment, $id){

            DB::table('appointments')
                ->where('id', $appointment->id)
                ->where('lock_version', $appointment->lock_version)
                ->update(['lock_version' => $appointment->lock_version +1,
                        'patient_id' => $id
                ]);
            
        });
    }
}


