<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DB;
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

        $user = new User;
        $user->email = 'patient-test02@gmail.com';
        $user->name = 'patient1_name';
        $user->last_name = 'patient1_lastname';
        $user->ensurance_id = '54535344';
        $user->phone_number = '43256434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user);

        
        $u2 = User::where('email','patient-test02@gmail.com')->first();
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
        $user->email = 'doctor-test01@gmail.com';
        $user->name = 'doctor1_name';
        $user->last_name = 'doctor2_lastname';
        $user->ensurance_id = '9485235';
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
        $appointmentToBeReserved->approved = 0;
        $appointmentToBeReserved->done = 0;
        $appointmentToBeReserved->save();

        $price = new Price();
        $price->price = 1000;
        $price->clinic_id = 1;
        $price->appointment_type_id = 1;
        $patient = Patient::first();
        $user=$patient->user()->first();

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

        // $clinic = Clinic::with(['doctors' => function($q) use ($at) {
        //     $q->with(['appointmentTypes'=> function($q) use ($at){
        //         $q->where('name',$at->name);
        //     }]);
        // }])->first();


        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('POST', '/api/appointment/request/'.$doctor->id, ['date' => '2020-12-12 12:00:00', 'appointment_type' => $at->id]);
        
        
            

    }
}
