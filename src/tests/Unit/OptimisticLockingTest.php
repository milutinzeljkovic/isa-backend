<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Reshadman\OptimisticLocking\StaleModelLockingException;

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
        //prvi zakazuje pregled
       /* $truth = Appointment::create([
            'date' => '2020-01-01 11:03:08',
            'price' => '5000',
            'clinic_id' => '1',
            'doctor_id' => '1'
        ]);*/

        $truth = Appointment::find('7');

        $first = Appointment::find($truth->id);
        $second = Appointment::find($truth->id);
        

        $this->expectException(StaleModelLockingException::class);

        $first->patient_id = '2';
        $this->assertTrue($first->save());

        try {
            $second->patient_id = '5';
            $second->save();
        } catch (StaleModelLockingException $e) {
            $fetchedAfterFirstUpdate = Appointment::find('7');
            $this->assertEquals($fetchedAfterFirstUpdate->user_id, '2');
            $this->assertEquals($fetchedAfterFirstUpdate->lock_version, $first->lock_version);
            $this->assertEquals($fetchedAfterFirstUpdate->lock_version, $truth->lock_version + 1);
            throw $e;
        }


    }
}
