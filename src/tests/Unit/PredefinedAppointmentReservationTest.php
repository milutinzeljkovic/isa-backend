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
        $user1 = '1';
        $user2 = '2';
        $user = new User;
        $user->email = 'patient1@gmail.com';
        $user->name = 'patient1_name';
        $user->last_name = 'patient1_lastname';
        $user->ensurance_id = '94859335';
        $user->phone_number = '43256434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user);

        $user->email = 'patient2@gmail.com';
        $user->name = 'patient2_name';
        $user->last_name = 'patient2_lastname';
        $user->ensurance_id = '94859335';
        $user->phone_number = '43256434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user);

        $appointment = DB::table('appointments')->where('id','1')->first();

        $this->updateAppointment($appointment, $user2);
        $this->updateAppointment($appointment, $user1);
        $this->assertEquals('2', $appointment->patient_id);

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


