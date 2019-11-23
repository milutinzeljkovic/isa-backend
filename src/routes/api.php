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

Route::group([
    'middleware' => ['api', 'jwt.verify', 'jsonify'],
    'prefix' => 'patients'
], function ($router){
    Route::get('','PatientsController@getPatients')->middleware('can:fetch,App\Patient');
    Route::get('accept/{id}', 'PatientsController@accept')->middleware('can:accept,App\Patient');
    Route::get('decline/{id}', 'PatientsController@decline')->middleware('can:decline,App\Patient');
    Route::get('{id}', 'PatientsController@view');
});