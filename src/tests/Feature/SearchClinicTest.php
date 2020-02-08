<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Patient;

class SearchClinicTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = new User;
        $user->email = 'patient1234@gmail.com';
        $user->name = 'patient_name';
        $user->last_name = 'patient_lastname';
        $user->ensurance_id = '56456789';
        $user->phone_number = '43256434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user);

        $response = $this->get('/api/clinics');
        $response->assertStatus(401);

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

        $response
            ->assertStatus(200);
        
        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('GET', '/api/clinics');

        $response
            ->assertStatus(200);

        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('GET', '/api/clinics?name=nada diva');

        $response
            ->assertStatus(200);
        
        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('GET', '/api/clinics?name=123');

        $response
            ->assertStatus(200)
            ->assertExactJson([]);

        $response = $this->withHeaders([
            'X-Header' => 'Value',
            'Authorization' => $bearer,
        ])->json('GET', '/api/clinics?name=123$address=bulevar');

        $response
            ->assertStatus(200)
            ->assertExactJson([]);
    }
}
