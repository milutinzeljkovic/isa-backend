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
        $user1 = new User;
        $user1->email = 'doca@gmail.com';
        $user1->name = 'doca_name';
        $user1->last_name = 'doca_lastname';
        $user1->ensurance_id = '64645754';
        $user1->phone_number = '43256434';
        $user1->address = 'address';
        $user1->city = 'city';
        $user1->state = 'state';
        $user1->password = \Hash::make('pass123', 'password');
        $user1->has_loggedin = 1;
        $doctor = new Doctor();
        $doctor->save();
        $doctor->user()->save($user1);

        $patient = Patient::first();

        if($patient == null){
            $user = new User;
            $user->email = 'patient@gmail.com';
            $user->name = 'patient_name';
            $user->last_name = 'patient_lastname';
            $user->ensurance_id = '94859335';
            $user->phone_number = '43256434';
            $user->address = 'address';
            $user->city = 'city';
            $user->state = 'state';
            $user->password = \Hash::make('pass123', 'password');
            $user->has_loggedin = 1;
            $patient = new Patient();
            $patient->save();
            $patient->user()->save($user);
        }

        


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

        $c = new Clinic();
        $c->name='name';
        $c->address='adresa';
        $c->description='dobra';
        $c->clinical_center_id=1;
        $c->save();

        $c1 = new Clinic();
        $c1->name='name1';
        $c1->address='adresa1';
        $c1->description='dobra1';
        $c1->clinical_center_id=1;
        $c1->save();

        $clinics = Clinic::all();
        
        $appType = AppointmentType::first();
        if($appType == null){
            $appType= new AppointmentType();
            $appType->name='pregled grla';
            $appType->save();



        }

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
