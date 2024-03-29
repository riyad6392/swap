<?php

use App\Http\Controllers\Auth\Admins\ForgetPasswordController;
use App\Http\Controllers\Auth\Admins\LoginController;
use App\Http\Controllers\Auth\Admins\RegistrationController;
use Illuminate\Support\Facades\Route;



Route::post('admin/register', [RegistrationController::class, 'register']);
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login');
Route::post('admin/forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('admin.forgot-password');
Route::post('admin/reset-password', [ForgetPasswordController::class, 'resetPassword'])->name('admin.reset-password');
Route::post('admin/refresh-token', [LoginController::class, 'refreshToken']);

Route::group(['middleware' => 'auth:admin-api'], function () {
    Route::get('/admin-test' , function(){
        foreach (array_keys(config('auth.guards')) as $guard) {

            if (auth()->guard($guard)->check()) return $guard;
        }
        return null;
        return response()->json(['success' => true, 'message' => 'You are authorized to access this data!'], 200);
    })->name('data');

    Route::post('admin/logout', [LoginController::class, 'logout']);

//    Route::resource('product', \App\Http\Controllers\ProductController::class);

});
