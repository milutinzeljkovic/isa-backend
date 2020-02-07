<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Clinic;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClinicSearchTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $clinic = Clinic::find(1);
        $clinics = Clinic::where('name','%like%',$clinic->name)->get();
        $this->assertTrue($clinics != []);

        $clinics = Clinic::where('address', '%like%', $clinic->address)->get();
        $this->assertTrue($clinics != []);

        $clinics = Clinic::where('stars_count', '=', $clinic->stars_count)->get();
        $this->assertTrue($clinics != []);

        $clinics = Clinic::where('stars_count', '=', $clinic->stars_count)
            ->where('address', '%like%', $clinic->address)
            ->where('name','%like%',$clinic->name)
        ->get();
        $this->assertTrue($clinics != []);

    }
}
