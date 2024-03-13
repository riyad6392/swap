<?php

use App\Http\Controllers\Auth\Admins\LoginController;
use App\Http\Controllers\Auth\Admins\RegistrationController;
use Illuminate\Support\Facades\Route;



Route::post('admin/register', [RegistrationController::class, 'register'])->name('admin.register');
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login');

Route::group(['middleware' => 'auth:admins-api'], function () {
    Route::get('/admin-test' , function(){
        return response()->json(['success' => true, 'message' => 'You are authorized to access this data!'], 200);
    })->name('data');

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('product', \App\Http\Controllers\ProductController::class);

});
