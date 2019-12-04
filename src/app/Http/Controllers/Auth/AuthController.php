<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Http\Requests\RegisterApiRequest;
use App\Http\Controllers\Controller;
use App\Http\Services\UserService;

class AuthController extends Controller
{
    protected $userService;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register new user and get a JWT token
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $credentials = $request->only('name', 'last_name', 'email', 'password', 'ensurance_id', 'city', 'state', 'phone_number', 'address');
        return $this->userService->register($credentials);
    }

    public function registerMedicalStaff(Request $request)
    {
        $credentials = $request->only('name', 'last_name', 'email', 'password', 'ensurance_id', 'city', 'state', 'phone_number', 'address', 'role');
        return $this->userService->registerMedicalStaff($credentials);
    }

    public function registerClinicAdmin(RegisterApiRequest $request, $clinic_id)
    {
        $credentials = $request->only('name', 'last_name', 'email', 'password', 'ensurance_id', 'city', 'state', 'phone_number', 'address');
        return $this->userService->registerClinicAdmin($credentials, $clinic_id);
    }

    public function registerClinicalCenterAdmin(RegisterApiRequest $request)
    {
        $credentials = $request->only('name', 'last_name', 'email', 'password', 'ensurance_id', 'city', 'state', 'phone_number', 'address');
        return $this->userService->registerClinicalCenterAdmin($credentials);
    }



    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
     //   \Slack::to('#isa-logs')->send('new login attempt');

        $credentials = $request->only('email', 'password');

        return $this->userService->login($credentials);
    }

    public function changePassword(Request $request){

        $credentials = $request->only('email', 'password');
        $newPassword = $request->only( 'new_password');
        return $this->userService->changePassword($credentials, $newPassword);


    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->userService->me();
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        return $this->userService->logout();
    }

        /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->userService->refreshToken();
    }

    //dekriptovanje dela url koji korisnik poseti nakon odobrenja zahteva http://localhost:8000/api/auth/confirm/asufhduih23uio49unao9812390haslnmcxasd
    public function activate($encryptedId)
    {
        return $this->userService->activate($encryptedId);
    }
    

}
