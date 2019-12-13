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
        $input = array('id'=> '7');
        $user1 = '2';
        $user2 = '5';
        
        $this->updateAppointment1($input, $user1);
        $this->updateAppointment2($input, $user2);

        $appointment = DB::table('appointments')->where('id','7')->first();
        $this->assertEquals('2', $appointment->patient_id);

    }

    public function updateAppointment1($input, $id)
    {
        DB::transaction(function () {
            $appointment = DB::table('appointments')->where('id','7')->first();

            DB::table('appointments')
                ->where('id', '7')
                ->where('lock_version', $appointment->lock_version)
                ->update(['patient_id' => '2']);
            
            DB::table('appointments')
                ->where('id', '7')
                ->update(['lock_version' => $appointment->lock_version +1]);
            
        });
    }
    public function updateAppointment2($input, $id)
    {
        DB::transaction(function () {
            $appointment = DB::table('appointments')->where('id','7')->first();

            DB::table('appointments')
                ->where('id', '7')
                ->where('lock_version', '9')
                ->update(['patient_id' => '5']);

        });
    }
}
