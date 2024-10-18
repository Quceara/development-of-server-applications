<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controller;
use App\Http\Controllers\AuthenticationController;

Route::post('/auth/login',[AuthenticationController::class,'login'])->name('login');

Route::middleware(\App\Http\Middleware\RegisterCheckTokens::class)->group(function () {
    Route::post('/auth/register', [AuthenticationController::class, 'register'])->name('register');
});
Route::middleware(\App\Http\Middleware\OtherCheckTokens::class)->group(function () {
    Route::post('/auth/out',[AuthenticationController::class,'logout'])->name('logout');
    Route::post('/auth/out_all',[AuthenticationController::class,'logoutAll'])->name('logoutAll');
    Route::get('/auth/me',[AuthenticationController::class,'getToken'])->name('users');
    Route::get('/auth/tokens',[AuthenticationController::class,'getAllToken'])->name('Tokens');
    Route::post('/auth/change/password',[AuthenticationController::class,'changePassword'])->name('changePassword');
});
Route::middleware(\App\Http\Middleware\RefreshCheckTokens::class)->group(function () {
    Route::post('/auth/refresh', [AuthenticationController::class, 'refreshToken'])->name('refresh');
});

