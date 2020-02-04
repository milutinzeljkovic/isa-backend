<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Reshadman\OptimisticLocking\StaleModelLockingException;
use Illuminate\Support\Facades\DB;

use App\Appointment;


class OptimisticLockingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user1 = '2';
        $user2 = '5';

        $appointment = DB::table('appointments')->where('id','1')->first();
        $this->updateAppointment($appointment, $user2);
        $this->updateAppointment($appointment, $user1);
        $this->assertEquals('5', $appointment->patient_id);

    }


    public static function updateAppointment($appointment, $id)
    {
        DB::transaction(function () use($appointment, $id){

            DB::table('appointments')
                ->where('id', $appointment->id)
                ->where('lock_version', $appointment->lock_version)
                ->update(['patient_id' => $id]);
            
            DB::table('appointments')
                ->where('id', $appointment->id)
                ->update(['lock_version' => $appointment->lock_version +1]);
            
        });
    }
}


