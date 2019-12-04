<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Crypt;
use App\User;
use App\Patient;
use App\ClinicalCenterAdmin;
use App\Nurse;
use Auth;
use App\Doctor;
use App\ClinicAdmin;
use Auth;




class UserService
{

    /**
     * Register User and get a JWT token.
     *
     * @param  array  $userData
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(array $userData)
    {
        $user = new User;
        $user->email = array_get($userData, 'email');
        $user->name = array_get($userData, 'name');
        $user->last_name = array_get($userData, 'last_name');
        $user->ensurance_id = array_get($userData, 'ensurance_id');
        $user->phone_number = array_get($userData, 'phone_number');
        $user->last_name = array_get($userData, 'last_name');
        $user->address = array_get($userData, 'address');
        $user->city = array_get($userData, 'city');
        $user->state = array_get($userData, 'state');
        $user->password = \Hash::make(array_get($userData, 'password'));
        $user->has_loggedin = 1;
        if($user->email == 'admin@admin.rs')
        {
            $admin = new ClinicalCenterAdmin();
            $admin->save();
            
            if ($admin->user()->save($user)) {
                return $this->login($userData);
            }

            return response()->json(['error' => 'Something terrible happened'], 500);

        }

        if($user->email == 'nurse@nurse.rs'){
            $nurse = new Nurse();
            $nurse->save();

            if($nurse->user()->save($user)){
                return $this->login($userData);
 
            }

            return response()->json(['error' => 'Something terrible happened'], 500);

        }

        if($user->email == 'doctor@doctor.rs'){
            $doctor = new Doctor();
            $doctor->save();

            if($doctor->user()->save($user)){
                return $this->login($userData);
 
            }

            return response()->json(['error' => 'Something terrible happened'], 500);

        }

        if($user->email == 'clinic@clinic.rs'){
            $ca = new ClinicAdmin();
            $ca->save();

            if($ca->user()->save($user)){
                return $this->login($userData);
 
            }

            return response()->json(['error' => 'Something terrible happened'], 500);

        }

        if($user->email == 'nurse@nurse.rs'){
            $nurse = new Nurse();
            $nurse->save();

            if($nurse->user()->save($user)){
                return $this->login($userData);
 
            }

            return response()->json(['error' => 'Something terrible happened'], 500);

        }
        $patient = new Patient();
        $patient->save();

        if ($patient->user()->save($user)) {
            return $this->login($userData);
        }

        return response()->json(['error' => 'Something terrible happened'], 500);
    }

    public function activate($encryptedId)
    {
        $id = Crypt::decryptString($encryptedId);
        $user = User::findOrFail($id);
        $user->activated = 1;
        $user->save();
        
        return response()->json(['message' => 'Account successfully activated']);
    }

    

    /**
     * Get a JWT token via given credentials.
     *
     * @param  array  $userData
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(array $userData)
    {
        $user = User::where('email',array_get($userData, 'email'))->first();
        /**if($user->confirmed == 0){
            return response()->json(['error' => 'email not confirmed'], 401);
        }*/

        if($user->activated == 0){
            return response()->json(['error' => 'account not activated'], 401);
        }
        
        if ($token = $this->guard()->attempt($userData)) {
            return $this->respondWithToken($token);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer'
        ]);
    }

    public function refreshToken()
    {
        return $this->respondWithToken(auth()->refresh());

    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return \Auth::guard();
    }

    public function registerMedicalStaff(array $userData)
    {
        //clinic_id
        $currentUser = Auth::user();

        $clinicAdmin = $currentUser->userable()->get()[0];


        $user = new User;
        $user->email = array_get($userData, 'email');
        $user->name = array_get($userData, 'name');
        $user->last_name = array_get($userData, 'last_name');
        $user->ensurance_id = array_get($userData, 'ensurance_id');
        $user->phone_number = array_get($userData, 'phone_number');
        $user->address = array_get($userData, 'address');
        $user->city = array_get($userData, 'city');
        $user->state = array_get($userData, 'state');
        $user->password = \Hash::make(array_get($userData, 'password'));
        $user->confirmed = 1;
        $user->activated = 1;


        $role = array_get($userData, 'role');

        if($role === 'nurse'){
            $nurse = new Nurse();
            $nurse->clinic_id = $clinicAdmin->clinic_id;
            $nurse->save();
            $nurse->user()->save($user);

            return response()->json(['created' => 'Nurse has been registered'], 201);
        }

        if($role === 'doctor'){
            $doctor = new Doctor();
            $doctor->clinic_id = $clinicAdmin->clinic_id;
            $doctor->save();
            $doctor->user()->save($user);
            
            return response()->json(['created' => 'Doctor has been registered'], 201);
        }

        return response()->json(['error' => 'Something terrible happened'], 500);
    }

    public function registerClinicAdmin(array $userData, $clinic_id)
    {

        $user = new User;
        $user->email = array_get($userData, 'email');
        $user->name = array_get($userData, 'name');
        $user->last_name = array_get($userData, 'last_name');
        $user->ensurance_id = array_get($userData, 'ensurance_id');
        $user->phone_number = array_get($userData, 'phone_number');
        $user->address = array_get($userData, 'address');
        $user->city = array_get($userData, 'city');
        $user->state = array_get($userData, 'state');
        $user->password = \Hash::make(array_get($userData, 'password'));
        $user->confirmed = 1;
        $user->activated = 1;

        $clinicAdmin = new ClinicAdmin();
        $clinicAdmin->clinic_id = $clinic_id;
        $clinicAdmin->save();
        $clinicAdmin->user()->save($user);
        
        return response()->json(['created' => 'Clinic admin has been registered'], 201);
    }

    public function registerClinicalCenterAdmin(array $userData)
    {
        $currentUser = Auth::user();

        $clinicalCenterAdmin = $currentUser->userable()->get()[0];


        $user = new User;
        $user->email = array_get($userData, 'email');
        $user->name = array_get($userData, 'name');
        $user->last_name = array_get($userData, 'last_name');
        $user->ensurance_id = array_get($userData, 'ensurance_id');
        $user->phone_number = array_get($userData, 'phone_number');
        $user->address = array_get($userData, 'address');
        $user->city = array_get($userData, 'city');
        $user->state = array_get($userData, 'state');
        $user->password = \Hash::make(array_get($userData, 'password'));
        $user->confirmed = 1;
        $user->activated = 1;

        $newClinicalCenterAdmin = new ClinicalCenterAdmin();
        $newClinicalCenterAdmin->clinical_center_id = $clinicalCenterAdmin->clinical_center_id;
        $newClinicalCenterAdmin->save();
        $newClinicalCenterAdmin->user()->save($user);
        
        return response()->json(['created' => 'Clinical center admin has been registered'], 201);
    }

    public function changePassword(array $userData,array $newPassword){
        $user = User::where('email',array_get($userData, 'email'))->first();
        $password = array_get($userData, 'password');
        if (\Hash::check($password, $user->password)) {
            $user->password = \Hash::make(array_get($newPassword, 'new_password'));
            $user->has_loggedin = 1;
            $user->save();

            return response()->json(['changed' => 'Password has been changed'], 201);

        }

        return response()->json(['error' => 'Wrong old password'], 401);
    }



}
