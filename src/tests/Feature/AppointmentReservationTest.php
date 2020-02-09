<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Doctor;
use App\Clinic;
use App\Appointment;
use App\AppointmentType;
use Carbon\Carbon;
use App\Patient;
use Illuminate\Support\Facades\Hash;




class AppointmentReservationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {


        $patient = Patient::all()[0];
        $user = $patient->user()->first();

        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', ['email' => $user->email, 'password' => 'password']);

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


        $at = AppointmentType::first();
        $doctor = Doctor::first();

        $clinic = Clinic::with(['doctors' => function($q) use ($at) {
            $q->with(['appointmentTypes'=> function($q) use ($at){
                $q->where('name',$at->name);
            }]);
        }])->first();


        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('POST', '/api/appointment/request/'.$doctor->id, ['date' => '2020-12-12 12:00:00', 'appointment_type' => $at->id]);
        
        
        // $response
        //     ->assertStatus(200);

    }
}
