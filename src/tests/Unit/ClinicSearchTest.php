<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Clinic;
use App\ClinicalCenter;

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
        $clinicc = new ClinicalCenter();
        $clinicc->name='center';
        $clinicc->save();

        $clinic = new Clinic();
        $clinic->name='name';
        $clinic->address='adresa';
        $clinic->description='dobra';
        $clinic->clinical_center_id=1;
        $clinic->save();
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
