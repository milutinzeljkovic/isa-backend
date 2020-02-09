<?php

use Illuminate\Database\Seeder;
use App\ClinicalCenter;
use App\Clinic;
use App\OperationsRoom;

class ClinicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cc=new ClinicalCenter();
        $cc->name="klinicki centar";
        $cc->save();
        
        $c1 = new Clinic();
        $c1->name='klinika';
        $c1->address='adresa';
        $c1->description='dobra';
        $c1->clinical_center_id=$cc->id;
        $c1->save();

        $c2 = new Clinic();
        $c2->name='klinika2';
        $c2->address='adresa2';
        $c2->description='dobra';
        $c2->clinical_center_id=$cc->id;
        $c2->save();

        $oproom = new OperationsRoom();
        $oproom->name='soba1';
        $oproom->number=1;
        $oproom->clinic_id=$c1->id;
        $oproom->save();

        $oproom = new OperationsRoom();
        $oproom->name='soba2';
        $oproom->number=12;
        $oproom->clinic_id=$c2->id;
        $oproom->save();
        

    }
}
