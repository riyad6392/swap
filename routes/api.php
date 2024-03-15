<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgetPasswordController;
use App\Http\Controllers\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('register', [RegistrationController::class, 'register'])->name('register');
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('forgot-password');
Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword'])->name('reset-password');
Route::post('refresh-token', [ForgotPasswordController::class, 'getTokenAndRefreshTokenByRefreshToken']);


Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/test' , function(){
        return response()->json(['success' => true, 'message' => 'You are authorized to access this data!'], 200);
    })->name('data');

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::resource('product', \App\Http\Controllers\ProductController::class);

    Route::resource('plan', \App\Http\Controllers\PlanController::class);

    Route::group(['middleware' => 'check.subscription'], function () {
        Route::get('subscribe-check', function () {
            return response()->json(['success' => true, 'message' => 'You are subscribed to a plan.'], 200);
        })->name('subscribe-check');
        Route::resource('category', \App\Http\Controllers\CategoryController::class);
    });

});

require __DIR__.'/admin/admin_api.php';
