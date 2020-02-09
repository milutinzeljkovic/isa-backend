<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Appointment;
use App\AppointmentType;
use App\Clinic;
use Carbon\Carbon;
use App\Patient;
use App\Price;
use App\User;
use App\ClinicalCenter;
use App\Doctor;
use App\OperationsRoom;



class PredefinedAppointmentReservationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = new User;
        $user->email = 'patient-test03@gmail.com';
        $user->name = 'patient1_name';
        $user->last_name = 'patient1_lastname';
        $user->ensurance_id = '65465464';
        $user->phone_number = '43256434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user);

        
        $u2 = User::where('email','patient-test03@gmail.com')->first();
        $u2->activated = 1;
        $u2->confirmed = 1;
        $u2->save();

        $appType = new AppointmentType();
        $appType->name='imatipapregleda';
        $appType->save();

        $clinicc = new ClinicalCenter();
        $clinicc->name='center';
        $clinicc->save();

        $clinic = new Clinic();
        $clinic->name='name';
        $clinic->address='adresa';
        $clinic->description='dobra';
        $clinic->clinical_center_id=1;
        $clinic->save();

        $user = new User;
        $user->email = 'doctor-test02@gmail.com';
        $user->name = 'doctor1_name';
        $user->last_name = 'doctor2_lastname';
        $user->ensurance_id = '3443442';
        $user->phone_number = '4236434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $d = new Doctor();
        $d->save();
        $d->user()->save($user);

        $opr=new OperationsRoom();
        $opr->name='sdad';
        $opr->number=1;
        $opr->save();

        

        $appointmentToBeReserved = new Appointment();
        $appointmentToBeReserved->price = 1000;
        $appointmentToBeReserved->doctor_id = 1;
        $appointmentToBeReserved->clinic_id = 1;
        $appointmentToBeReserved->appointment_type_id = 1;
        $appointmentToBeReserved->operations_room_id = 1;
        $appointmentToBeReserved->date = '2020-12-12 12:00:00';
        $appointmentToBeReserved->save();

        $price = new Price();
        $price->price = 1000;
        $price->clinic_id = 1;
        $price->appointment_type_id = 1;


        $patient1 = Patient::all()[0];
        $user1 = $patient1->user()->first();
        $response1 = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', ['email' => $user1->email, 'password' => 'password']);
        $response1
            ->assertStatus(200)
            ->assertJson([
                'access_token' => true,
            ]);
        $token1 = $response1->json()['access_token'];
        $bearer1 = "bearer " .$token1;


        $patient2 = Patient::all()[1];
        $user2 = $patient2->user()->first();
        $response2 = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', ['email' => $user2->email, 'password' => 'password']);
        $response2
            ->assertStatus(200)
            ->assertJson([
                'access_token' => true,
            ]);
        $token2 = $response2->json()['access_token'];
        $bearer2 = "bearer " .$token2;

        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer1
        ])->json('GET', '/api/appointment');
        
        
        
        $appointmentId = Appointment::where('patient_id',null)->first()->id;


        $response1 = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer1
        ])->json('POST', '/api/appointment/reserve/'.$appointmentId);

        $this->assertTrue($appointmentId == 1);

        $response2 = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer2
        ])->json('POST', '/api/appointment/reserve/'.$appointmentId);
        
       

    }
}
