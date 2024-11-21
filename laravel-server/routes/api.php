<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controller;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChangeLogController;
Route::post('/auth/login',[AuthenticationController::class,'login']);

Route::middleware(\App\Http\Middleware\EnsureUserLoggedOut::class)->post('/auth/register', [AuthenticationController::class, 'register']);

Route::middleware(\App\Http\Middleware\CheckRefreshToken::class)->post('/auth/refresh', [AuthenticationController::class, 'refreshToken']);

Route::middleware(\App\Http\Middleware\CheckAccessToken::class)->group(function () {

    Route::post('/auth/out',[AuthenticationController::class,'logout']);
    Route::post('/auth/out_all',[AuthenticationController::class,'logoutAll']);
    Route::get('/auth/me',[AuthenticationController::class,'getToken']);
    Route::get('/auth/tokens',[AuthenticationController::class,'getAllToken']);
    Route::post('/auth/change/password',[AuthenticationController::class,'changePassword']);
    Route::put('/auth/2fa/update',[AuthenticationController::class,'update2faStatus']);

    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':get-list-user')
	->get('/ref/user', [UserController::class, 'getListUser']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':read-user')
	->get('/ref/user/{id}/role', [UserController::class, 'getUserRoles']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':update-user')
	->post('/ref/user/{id}/role', [UserController::class, 'assignRole']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':delete-user')
	->delete('/ref/user/{id}/role/{role_id}', [UserController::class, 'deleteRole']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':delete-user')
	->delete('/ref/user/{id}/role/{role_id}/soft', [UserController::class, 'softDeleteRole']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':restore-user')
	->post('/ref/user/{id}/role/{role_id}/restore', [UserController::class, 'restoreRole']);

    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':get-list-role')
        ->get('/ref/policy/role', [RoleController::class, 'getListRole']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':read-role')
        ->get('/ref/policy/role/{id}', [RoleController::class, 'readRole']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':create-role')
        ->post('/ref/policy/role', [RoleController::class, 'createRole']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':update-role')
        ->put('/ref/policy/role/{id}', [RoleController::class, 'update']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':update-role')
        ->delete('/ref/policy/role/{id}/permission/soft', [RoleController::class, 'softDeletePermissions']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':update-role')
        ->delete('/ref/policy/role/{id}/permission', [RoleController::class, 'removePermissions']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':delete-role')
        ->delete('/ref/policy/role/{id}', [RoleController::class, 'destroy']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':soft-delete-role')
        ->delete('/ref/policy/role/{id}/soft', [RoleController::class, 'softDelete']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':restore-role')
        ->post('/ref/policy/role/{id}/restore', [RoleController::class, 'restore']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':update-role')
        ->post('/ref/policy/role/{id}/permission/restore', [RoleController::class, 'permissionRestore']);

    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':get-list-permission')
        ->get('/ref/policy/permission', [PermissionController::class, 'getListPermission']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':read-permission')
        ->get('/ref/policy/permission/{id}', [PermissionController::class, 'readPermission']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':create-permission')
        ->post('/ref/policy/permission', [PermissionController::class, 'createPermission']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':update-permission')
        ->put('/ref/policy/permission/{id}', [PermissionController::class, 'update']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':delete-permission')
        ->delete('/ref/policy/permission/{id}', [PermissionController::class, 'destroy']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':soft-delete-permission')
        ->delete('/ref/policy/permission/{id}/soft', [PermissionController::class, 'softDelete']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':restore-permission')
        ->post('/ref/policy/permission/{id}/restore', [PermissionController::class, 'restore']);

    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':getEntity-logs')
        ->get('ref/policy/{entity}/{id}/story', [ChangeLogController::class, 'getEntityLogs']);
    Route::middleware(\App\Http\Middleware\CheckPermission::class . ':restoreEntity-log')
        ->get('ref/policy/{entity}/{id}/{mutationId}/restore', [ChangeLogController::class, 'restoreEntityLog']);
});

