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
use App\Vacation;
use App\MedicalRecord;
use App\Utils\SimpleFactory;
use App\WorkingDay;



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
        if($user->activated == 0){
            $patient = $user->userable()->get()[0];
            $medicalRecord = new MedicalRecord();
            $medicalRecord->patient_id=$patient->id;
            $medicalRecord->save();
            $user->activated = 1;
            $user->save();
            
            return response()->json(['message' => 'Account successfully activated']);
        }

        return response()->json(['message' => 'Account was already activated']);     
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

        return response()->json(['error' => 'Incorrect credentials'], 401);
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

        $factory = new SimpleFactory();

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
            $nurse = $factory->createNurse();
            $nurse->clinic_id = $clinicAdmin->clinic_id;
            $nurse->save();
            $nurse->user()->save($user);

            $monday = $factory->createWorkingDay();
            $monday->from = array_get($userData, 'mondayFrom');
            $monday->to = array_get($userData, 'mondayTo');
            $monday->nurse_id = $nurse->id;
            $monday->day = 1;
            $monday->save();

            $tuesday = $factory->createWorkingDay();
            $tuesday->from = array_get($userData, 'tuesdayFrom');
            $tuesday->to = array_get($userData, 'tuesdayTo');
            $tuesday->nurse_id = $nurse->id;
            $tuesday->day = 2;
            $tuesday->save();

            $wednesday = $factory->createWorkingDay();
            $wednesday->from = array_get($userData, 'wednesdayFrom');
            $wednesday->to = array_get($userData, 'wednesdayTo');
            $wednesday->nurse_id = $nurse->id;
            $wednesday->day = 3;
            $wednesday->save();

            $thursday = $factory->createWorkingDay();
            $thursday->from = array_get($userData, 'thursdayFrom');
            $thursday->to = array_get($userData, 'thursdayTo');
            $thursday->nurse_id = $nurse->id;
            $thursday->day = 4;
            $thursday->save();

            $friday = $factory->createWorkingDay();
            $friday->from = array_get($userData, 'fridayFrom');
            $friday->to = array_get($userData, 'fridayTo');
            $friday->nurse_id = $nurse->id;
            $friday->day = 5;
            $friday->save();

            $saturday = $factory->createWorkingDay();
            $saturday->from = array_get($userData, 'saturdayFrom');
            $saturday->to = array_get($userData, 'saturdayTo');
            $saturday->nurse_id = $nurse->id;
            $saturday->day = 6;
            $saturday->save();

            $sunday = $factory->createWorkingDay();
            $sunday->from = array_get($userData, 'sundayFrom');
            $sunday->to = array_get($userData, 'sundayTo');
            $sunday->nurse_id = $nurse->id;
            $sunday->day = 0;
            $sunday->save();

            return response()->json(['created' => 'Nurse has been registered'], 201);
        }

        if($role === 'doctor'){
            $doctor = $factory->createDoctor();
            $doctor->clinic_id = $clinicAdmin->clinic_id;
            $doctor->save();
            $doctor->user()->save($user);

            $monday = $factory->createWorkingDay();
            $monday->from = array_get($userData, 'mondayFrom');
            $monday->to = array_get($userData, 'mondayTo');
            $monday->doctor_id = $doctor->id;
            $monday->day = 1;
            $monday->save();

            $tuesday = $factory->createWorkingDay();
            $tuesday->from = array_get($userData, 'tuesdayFrom');
            $tuesday->to = array_get($userData, 'tuesdayTo');
            $tuesday->doctor_id = $doctor->id;
            $tuesday->day = 2;
            $tuesday->save();

            $wednesday = $factory->createWorkingDay();
            $wednesday->from = array_get($userData, 'wednesdayFrom');
            $wednesday->to = array_get($userData, 'wednesdayTo');
            $wednesday->doctor_id = $doctor->id;
            $wednesday->day = 3;
            $wednesday->save();

            $thursday = $factory->createWorkingDay();
            $thursday->from = array_get($userData, 'thursdayFrom');
            $thursday->to = array_get($userData, 'thursdayTo');
            $thursday->doctor_id = $doctor->id;
            $thursday->day = 4;
            $thursday->save();

            $friday = $factory->createWorkingDay();
            $friday->from = array_get($userData, 'fridayFrom');
            $friday->to = array_get($userData, 'fridayTo');
            $friday->doctor_id = $doctor->id;
            $friday->day = 5;
            $friday->save();

            $saturday = $factory->createWorkingDay();
            $saturday->from = array_get($userData, 'saturdayFrom');
            $saturday->to = array_get($userData, 'saturdayTo');
            $saturday->doctor_id = $doctor->id;
            $saturday->day = 6;
            $saturday->save();

            $sunday = $factory->createWorkingDay();
            $sunday->from = array_get($userData, 'sundayFrom');
            $sunday->to = array_get($userData, 'sundayTo');
            $sunday->doctor_id = $doctor->id;
            $sunday->day = 0;
            $sunday->save();

            return response()->json(['created' => 'Doctor has been registered'], 201);
        }

        return response()->json(['error' => 'Something terrible happened'], 500);
    }

    public function addWorkingDays($id, $koJe, array $userData){

        if($koJe == 0){
            $monday = new WorkingDay();
            $monday->from = array_get($userData, 'mondayFrom');
            $monday->to = array_get($userData, 'mondayTo');
            $monday->nurse_id = $id;
            $monday->day = 1;
            $monday->save();

            $tuesday = new WorkingDay();
            $tuesday->from = array_get($userData, 'tuesdayFrom');
            $tuesday->to = array_get($userData, 'tuesdayTo');
            $tuesday->nurse_id = $id;
            $tuesday->day = 2;
            $tuesday->save();

            $wednesday = new WorkingDay();
            $wednesday->from = array_get($userData, 'wednesdayFrom');
            $wednesday->to = array_get($userData, 'wednesdayTo');
            $wednesday->nurse_id = $id;
            $wednesday->day = 3;
            $wednesday->save();

            $thursday = new WorkingDay();
            $thursday->from = array_get($userData, 'thursdayFrom');
            $thursday->to = array_get($userData, 'thursdayTo');
            $thursday->nurse_id = $id;
            $thursday->day = 4;
            $thursday->save();

            $friday = new WorkingDay();
            $friday->from = array_get($userData, 'fridayFrom');
            $friday->to = array_get($userData, 'fridayTo');
            $friday->nurse_id = $id;
            $friday->day = 5;
            $friday->save();

            $saturday = new WorkingDay();
            $saturday->from = array_get($userData, 'saturdayFrom');
            $saturday->to = array_get($userData, 'saturdayTo');
            $saturday->nurse_id = $id;
            $saturday->day = 6;
            $saturday->save();

            $sunday = new WorkingDay();
            $sunday->from = array_get($userData, 'sundayFrom');
            $sunday->to = array_get($userData, 'sundayTo');
            $sunday->nurse_id = $id;
            $sunday->day = 0;
            $sunday->save();
        }else {
            $monday = new WorkingDay();
            $monday->from = array_get($userData, 'mondayFrom');
            $monday->to = array_get($userData, 'mondayTo');
            $monday->doctor_id = $id;
            $monday->day = 1;
            $monday->save();

            $tuesday = new WorkingDay();
            $tuesday->from = array_get($userData, 'tuesdayFrom');
            $tuesday->to = array_get($userData, 'tuesdayTo');
            $tuesday->doctor_id = $id;
            $tuesday->day = 2;
            $tuesday->save();

            $wednesday = new WorkingDay();
            $wednesday->from = array_get($userData, 'wednesdayFrom');
            $wednesday->to = array_get($userData, 'wednesdayTo');
            $wednesday->doctor_id = $id;
            $wednesday->day = 3;
            $wednesday->save();

            $thursday = new WorkingDay();
            $thursday->from = array_get($userData, 'thursdayFrom');
            $thursday->to = array_get($userData, 'thursdayTo');
            $thursday->doctor_id = $id;
            $thursday->day = 4;
            $thursday->save();

            $friday = new WorkingDay();
            $friday->from = array_get($userData, 'fridayFrom');
            $friday->to = array_get($userData, 'fridayTo');
            $friday->doctor_id = $id;
            $friday->day = 5;
            $friday->save();

            $saturday = new WorkingDay();
            $saturday->from = array_get($userData, 'saturdayFrom');
            $saturday->to = array_get($userData, 'saturdayTo');
            $saturday->doctor_id = $id;
            $saturday->day = 6;
            $saturday->save();

            $sunday = new WorkingDay();
            $sunday->from = array_get($userData, 'sundayFrom');
            $sunday->to = array_get($userData, 'sundayTo');
            $sunday->doctor_id = $id;
            $sunday->day = 0;
            $sunday->save();
        }
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

    public function sendRequestForVacation(array $userData){
        $currentUser = Auth::user();

        $vacation = new Vacation;
        $vacation->from = array_get($userData, 'from');
        $vacation->to = array_get($userData, 'to');
        $vacation->user_id = $currentUser->id;

        $vacation->save();
        return response()->json(['created' => 'Request for vacation has been created'], 201);

    }


}
