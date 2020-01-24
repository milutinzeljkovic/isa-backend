<?php

use Illuminate\Database\Seeder;

class AppointmentTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\AppointmentType::class, 10)->create();
    }
}
