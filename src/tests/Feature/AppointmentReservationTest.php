<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Clinic;
use App\Appointment;
use App\AppointmentType;
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
        $patient = Patient::first();
        $user = $patient->user()->first();

        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', ['email' => $user->email, 'password' => '123']);

        $response
            ->assertStatus(200)
            ->assertJson([
                'access_token' => true,
            ]);
        $token = $response->json()['access_token'];

        $bearer = "bearer " .$token;

        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('GET', '/api/clinics');


        $clinics = Clinic::all();
        $appType = AppointmentType::first();

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

        $appointment = new Appointment();
        $appointment->date = '2020-12-15 15:00:00';
        $appointment->doctor_id = $doctor->id;
        $appointment->appointment_type_id = $appType->id;
        
        echo $appointment;

        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('POST', '/api/appointment/request/'.$appointment->doctor->id, ['date' => $appointment->date, 'appointment_type' => $appointment->appointment_type_id]);
        
        $response
            ->assertStatus(200);
    }
}
