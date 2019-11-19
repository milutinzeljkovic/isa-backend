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

    Route::post('register', 'Auth\AuthController@register');
    Route::post('login', 'Auth\AuthController@login');
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('me', 'Auth\AuthController@me');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::get('activate/{enryptedId}', 'Auth\AuthController@activate');
    
});

Route::get('test',function(){
    $encrypted = Crypt::encryptString('10');
    return $encrypted;

});


Route::get('decrypt',function(){
    $encrypted = 'eyJpdiI6InpCZDUwWkp2RUsyQjdPamY2cHVuUkE9PSIsInZhbHVlIjoiSFhuRElUcUM2UE03VmJuOUdiNGpuUT09IiwibWFjIjoiYjdkNmNjODkxOGQ2NTU5YTdiNTRiZDVjYmNhOWIxOTM4YTczNjFmNTIxMGU2M2JkOTQ2ODRmYTkxNDJiYWFiMCJ9';
    $decrypted = Crypt::decryptString($encrypted);
    return $decrypted;
});

Route::get('patients','PatientsController@getPatients');
Route::get('patients/accept/{id}', 'PatientsController@accept');
Route::get('patients/decline/{id}', 'PatientsController@decline');