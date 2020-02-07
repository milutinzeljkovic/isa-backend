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

class PredefinedAppointmentReservationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {

        $appointmentToBeReserved = new Appointment();
        $appointmentToBeReserved->price = 1000;
        $appointmentToBeReserved->doctor_id = 1;
        $appointmentToBeReserved->clinic_id = 1;
        $appointmentToBeReserved->operations_room_id = 1;
        $appointmentToBeReserved->date = '2020-12-12 12:00:00';
        $appointmentToBeReserved->save();

        $patient1 = Patient::all()[0];
        $user1 = $patient1->user()->first();

        $response1 = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', ['email' => $user1->email, 'password' => '123']);

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
        ])->json('POST', '/api/auth/login', ['email' => $user2->email, 'password' => '123']);

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
        
        $appointmentId = $response->original[0]->id;

        $response1 = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer1
        ])->json('POST', '/api/appointment/reserve/'.$appointmentId);

        $response2 = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer2
        ])->json('POST', '/api/appointment/reserve/'.$appointmentId);
        
        $response1
            ->assertStatus(200);
        
        
        $response2
            ->assertStatus(403);

    }
}
