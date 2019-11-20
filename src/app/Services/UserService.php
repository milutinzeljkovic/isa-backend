<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Crypt;
use App\User;
use App\Patient;
use App\ClinicalCenterAdmin;



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

        if($user->email == 'admin@admin.rs')
        {
            $admin = new ClinicalCenterAdmin();
            $admin->save();
            
            if ($admin->user()->save($user)) {
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
        if($user->confirmed == 0){
            return response()->json(['error' => 'email not confirmed'], 401);
        }

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



}
