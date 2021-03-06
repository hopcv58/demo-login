<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home')->middleware(['auth', '2fa']);

Route::get('user/activation/{token}', 'Auth\RegisterController@activateUser')->name('user.activate');

//2fa routes
Route::get('/2fa','PasswordSecurityController@show2faForm')->middleware(['auth', '2fa']);
Route::post('/generate2faSecret','PasswordSecurityController@generate2faSecret')->name('generate2faSecret')->middleware(['auth', '2fa']);;
Route::post('/2fa','PasswordSecurityController@enable2fa')->name('enable2fa')->middleware(['auth', '2fa']);
Route::post('/disable2fa','PasswordSecurityController@disable2fa')->name('disable2fa')->middleware(['auth', '2fa']);
Route::post('/2faVerify', function () {
    return redirect(URL()->previous());
})->name('2faVerify')->middleware(['auth', '2fa']);