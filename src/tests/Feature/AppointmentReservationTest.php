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
        // $user1 = new User;
        // $user1->email = 'doca@gmail.com';
        // $user1->name = 'doca_name';
        // $user1->last_name = 'doca_lastname';
        // $user1->ensurance_id = '64645754';
        // $user1->phone_number = '43256434';
        // $user1->address = 'address';
        // $user1->city = 'city';
        // $user1->state = 'state';
        // $user1->password = \Hash::make('password');
        // $user1->has_loggedin = 1;
        // $doctor = new Patient();
        // $doctor->save();
        // $doctor->user()->save($user1);

        // $patient = Patient::first();

        // if($patient == null){
        //     $user = new User;
        //     $user->email = 'patient@gmail.com';
        //     $user->name = 'patient_name';
        //     $user->last_name = 'patient_lastname';
        //     $user->ensurance_id = '94859335';
        //     $user->phone_number = '43256434';
        //     $user->address = 'address';
        //     $user->city = 'city';
        //     $user->state = 'state';
        //     $user->password = \Hash::make('password');
        //     $user->has_loggedin = 1;
        //     $patient = new Patient();
        //     $patient->save();
        //     $patient->user()->save($user);
        // }

        

        $patient = Patient::all()[0];
        $user = $patient->user()->first();

        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', ['email' => 'patient-test01@gmail.com', 'password' => 'password']);

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
        
        $response
            ->assertStatus(200);

        echo $response;
    }
}
