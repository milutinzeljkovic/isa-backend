<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Patient;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;
        $user->email = 'patient-test01@gmail.com';
        $user->name = 'patient1_name';
        $user->last_name = 'patient1_lastname';
        $user->ensurance_id = '94859335';
        $user->phone_number = '43256434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user);

        $user = new User;
        $user->email = 'patient-test02@gmail.com';
        $user->name = 'patient2_name';
        $user->last_name = 'patient2_lastname';
        $user->ensurance_id = '94859335';
        $user->phone_number = '43256434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user);

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
        $patient = new Doctor();
        $patient->save();
        $patient->user()->save($user);

        $user = new User;
        $user->email = 'doctor-test02@gmail.com';
        $user->name = 'doctor2_name';
        $user->last_name = 'doctor2_lastname';
        $user->ensurance_id = '9482235';
        $user->phone_number = '4216434';
        $user->address = 'address';
        $user->city = 'city';
        $user->state = 'state';
        $user->password = \Hash::make('password');
        $user->has_loggedin = 1;
        $patient = new Doctor();
        $patient->save();
        $patient->user()->save($user);
        




    }
}
