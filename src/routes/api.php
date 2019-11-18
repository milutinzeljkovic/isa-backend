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
    Route::get('confirm/{enryptedId}', 'Auth\AuthController@confirmAccount');
    
});

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('foo', function () {
        return 'Hello World pizda vam materina';
    });
});


Route::get('test',function(){
    $encrypted = Crypt::encryptString('10');
    return $encrypted;

});


Route::get('decrypt',function(){
    $encrypted = 'eyJpdiI6IkVJZHFGZmdDNUpDazh5Y2thZjBsdnc9PSIsInZhbHVlIjoiWU45VzhYVVo5Smd5K3pESjN4VjA2UT09IiwibWFjIjoiNWM1NTg5YTdjODc4YzkxNjIwOGY4Y2JmMmIyM2UzOGI5NThlYjg1Y2FlNzk1Nzg4ZjBkYjkxNThhNTYzNDI5NiJ9';
    $decrypted = Crypt::decryptString($encrypted);
    return $decrypted;

});