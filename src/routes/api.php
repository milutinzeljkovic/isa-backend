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
    Route::post('register/clinic-admin/{clinic_id}', 'Auth\AuthController@registerClinicAdmin')->middleware('can:addClinicAdmin,App\ClinicAdmin');
    Route::post('register/clinical-center-admin', 'Auth\AuthController@registerClinicalCenterAdmin')->middleware('can:addClinicAdmin,App\ClinicAdmin');
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
    Route::get('medical-record/{id}', 'PatientsController@medicalRecord')->middleware('can:viewMedicalRecord,App\Patient,id');
    Route::get('appointments/{id}', 'PatientsController@getAppointments');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'clinics'
], function ($router){
    Route::get('','ClinicController@index');
    Route::get('{id}','ClinicController@show');
    Route::post('','ClinicController@store')->middleware('can:add,App\Clinic');
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
    Route::get('operations','DoctorController@getOperations');

    Route::post('finish-report','DoctorController@medicalReportForAppointment');
    Route::get('get-data/{id}','DoctorController@getDataForDoctor');
    Route::post('shedule-operation','DoctorController@sheduleAnOperation');
    Route::get('calendar','DoctorController@getApointments');
    Route::get('{id}','DoctorController@show');
    Route::get('appointments/{id}', 'DoctorController@showDoctorAppointments');
    Route::get('','DoctorController@index');
    Route::delete('delete/{id}', 'DoctorController@destroy');
    Route::put('update/{id}', 'DoctorController@update');
    Route::get('booked/{id}', 'DoctorController@seeIfDoctorUsed');
    Route::delete('delete/{id}', 'DoctorController@destroy');
    Route::get('working-days/{id}', 'DoctorController@getWorkingHours');
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
    Route::post('change-date-operation', 'ClinicAdminController@changeDateoOfOperation');

    Route::post('add-duration', 'ClinicAdminController@addDuration');
    Route::post('edit-operations', 'ClinicAdminController@editOperation');
    Route::get('operations', 'ClinicAdminController@getOperations');
    Route::get('doctors', 'ClinicAdminController@getAllDoctors');
    Route::get('facilities', 'ClinicAdminController@getAllFacilities')->middleware('can:fetchFacilities,App\Clinic');
    Route::get('clinic', 'ClinicAdminController@getAdminsClinic');
    Route::put('clinic/update', 'ClinicAdminController@updateClinic')->middleware('can:update,App\Clinic');
    Route::get('doctors/clinic', 'ClinicAdminController@clinicDoctors');
    Route::post('reserve-operation', 'ClinicAdminController@reserveOperation');
    Route::post('reserve-appointment', 'ClinicAdminController@reserveAppointmentRoom');
    Route::get('pending-appointment-requests', 'ClinicAdminController@pendingAppointmentRequests');
    

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
    Route::put('update/{id}', 'OperatingRoomController@update');
    Route::delete('delete/{id}', 'OperatingRoomController@destroy');
    Route::get('used/{id}', 'OperatingRoomController@seeIfOpRoomBooked');
    Route::get('', 'OperatingRoomController@index');
    Route::get('availability/{id}', 'OperatingRoomController@getAppointments');
    Route::get('recommendation/{id}', 'OperatingRoomController@getFirstFreeDate');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'appointment-types'
],function ($router){
    Route::post('','AppointmentTypeController@store');
    Route::get('', 'AppointmentTypeController@getAllAppTypes');
    Route::get('/clinic', 'AppointmentTypeController@clinicAppointmentTypes');
    Route::delete('delete/{id}', 'AppointmentTypeController@destroy');
    Route::put('update/{id}', 'AppointmentTypeController@update');
    Route::get('used/{id}', 'AppointmentTypeController@seeIfAppTypeUsed');
});

Route::group([
    'middleware' => ['api', 'jsonify'],
    'prefix' => 'confirmations'
],function ($router){
    Route::get('confirm/{enryptedId}', 'AppointmentController@confirm');
    Route::get('decline/{enryptedId}', 'AppointmentController@decline');

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
    Route::post('/{id}', 'ReactionController@store')->middleware('can:rate,App\Clinic,id');
    Route::post('/doctor/{id}', 'ReactionController@storeDoctorRecension')->middleware('can:rateDoctor,App\Clinic,id');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'working-hours' 
], function ($router){
    Route::get('doctors/{id}', 'WorkingDayController@getDoctorsWorkingHours');
    Route::put('update-doctors/{id}', 'WorkingDayController@updateDoctorsWorkingHours');
});

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'vacation' 
], function ($router){
    Route::get('', 'VacationController@getVacationRequests');
    Route::put('approve/{id}', 'VacationController@approveVacationRequest');
    Route::put('decline/{id}', 'VacationController@declineVacationRequest');
});

