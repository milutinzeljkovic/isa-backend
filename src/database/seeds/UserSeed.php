<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Patient;
use App\Doctor;

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


        $user1 = new User;
        $user1->email = 'patient-test02@gmail.com';
        $user1->name = 'patient2_name';
        $user1->last_name = 'patient2_lastname';
        $user1->ensurance_id = '94859335';
        $user1->phone_number = '43256434';
        $user1->address = 'address';
        $user1->city = 'city';
        $user1->state = 'state';
        $user1->password = \Hash::make('password');
        $user1->has_loggedin = 1;
        $patient = new Patient();
        $patient->save();
        $patient->user()->save($user1);

        $u1 = User::where('email','patient-test02@gmail.com')->first();
        $u1->activated = 1;
        $u1->confirmed = 1;
        $u1->save();

        $u2 = User::where('email','patient-test01@gmail.com')->first();
        $u2->activated = 1;
        $u2->confirmed = 1;
        $u1->save();

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
