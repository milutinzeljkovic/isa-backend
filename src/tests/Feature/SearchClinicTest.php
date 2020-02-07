<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchClinicTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/api/clinics');
        $response->assertStatus(401);

        $response = $this->withHeaders([
            'X-Header' => 'Value',
        ])->json('POST', '/api/auth/login', ['email' => 'nada@gmail.com', 'password' => '123']);

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
