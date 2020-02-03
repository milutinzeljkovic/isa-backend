<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => ['api', 'jsonify'],
    'prefix' => 'auth'
], function ($router) {

    Route::post('vacation', 'Auth\AuthController@sendRequestForVacation');
    Route::post('change-password', 'Auth\AuthController@changePassword');
    Route::post('register', 'Auth\AuthController@register');
    Route::post('register/staff', 'Auth\AuthController@registerMedicalStaff');
    Route::post('register/clinic-admin/{clinic_id}', 'Auth\AuthController@registerClinicAdmin');
    Route::post('register/clinical-center-admin', 'Auth\AuthController@registerClinicalCenterAdmin');
    Route::post('login', 'Auth\AuthController@login');
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('me', 'Auth\AuthController@me');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::get('activate/{enryptedId}', 'Auth\AuthController@activate');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'patients'
], function ($router){
    Route::post('search', 'PatientsController@searchPatients');
    Route::get('clinic', 'PatientsController@getClinicsPatients');
    Route::get('','PatientsController@getPatients')->middleware('can:fetch,App\Patient');
    Route::get('accept/{id}', 'PatientsController@accept')->middleware('can:accept,App\Patient');
    Route::get('decline/{id}', 'PatientsController@decline')->middleware('can:decline,App\Patient');
    Route::get('{id}', 'PatientsController@view')->middleware('can:view,App\Patient,id');
    Route::put('{id}', 'PatientsController@update')->middleware('can:update,App\Patient,id');
    Route::get('show/{id}', 'PatientsController@view');
    Route::get('medical-record/{id}', 'PatientsController@medicalRecord');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'clinics'
], function ($router){
    Route::get('','ClinicController@index');
    Route::get('{id}','ClinicController@show');
    Route::post('','ClinicController@store');
    Route::get('/doctors/{clinic}','ClinicController@doctors');
});

Route::group([
    'middleware' => ['api', 'jsonify'],
    'prefix' => 'locations'
],function ($router){
    Route::get('','LocationController@searchLocation');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'doctors'
],function ($router){
    Route::post('finish-report','DoctorController@medicalReportForAppointment');
    Route::get('get-data/{id}','DoctorController@getDataForDoctor');

    Route::get('calendar','DoctorController@getApointments');
    Route::get('{id}','DoctorController@show');
    Route::get('appointments/{id}', 'DoctorController@showDoctorAppointments');
    Route::get('','DoctorController@index');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'medicine'
],function ($router){
    Route::get('','MedicineController@index');

    Route::post('add','MedicineController@store');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'diagnose'
],function ($router){
    Route::get('','DiagnoseController@index');

    Route::post('add','DiagnoseController@store');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'clinic-admin'
], function ($router){
    
    Route::get('doctors', 'ClinicAdminController@getAllDoctors');
    Route::get('facilities', 'ClinicAdminController@getAllFacilities');
    Route::get('clinic', 'ClinicAdminController@getAdminsClinic');
    Route::put('clinic/update', 'ClinicAdminController@updateClinic');
    Route::get('doctors/clinic', 'ClinicAdminController@clinicDoctors');

});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'prescriptions'
], function ($router){
    Route::get('', 'PrescriptionController@getPrescriptions');

    Route::put('check/{id}', 'PrescriptionController@update');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'operating-room'
], function ($router){
    
    Route::post('add', 'OperatingRoomController@store');
    Route::get('get', 'OperatingRoomController@getOpRooms');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'appointment-types'
],function ($router){
    Route::post('','AppointmentTypeController@store');
    Route::get('', 'AppointmentTypeController@getAllAppTypes');
    Route::get('/clinic', 'AppointmentTypeController@clinicAppointmentTypes');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'appointment' 
],function ($router){
    Route::post('add','AppointmentController@store');
    Route::post('reserve/{id}', 'AppointmentController@reserve')->middleware('can:reserve,App\Appointment,id');
    Route::get('history/{id}','AppointmentController@patientHistory');
    Route::post('request/{id}','AppointmentController@requestAppointment');
    Route::get('','AppointmentController@searchAppointment');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'reactions'
], function ($router){
    Route::post('/{id}', 'ReactionController@store');
    Route::post('/doctor/{id}', 'ReactionController@storeDoctorRecension');
});