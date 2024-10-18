<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\AuthenticationController;


Route::get('/info/server', [InfoController::class, 'serverInfo']);
Route::get('/info/client', [InfoController::class, 'clientInfo']);
Route::get('/info/database', [InfoController::class, 'databaseInfo']);

Route::get('/', function () {return view('welcome');});

Route::get('/form/login', function () {return view('form_login');});
Route::get('/form/refresh/token', function () {return view('form_refresh_token');});
Route::get('/form/logout', function () {return view('form_logout');});
Route::get('/form/register', function () {return view('form_register');});
Route::get('/form/register', function () {return view('form_register');});
Route::get('/form/change/password', function () {return view('form_change_password');});

