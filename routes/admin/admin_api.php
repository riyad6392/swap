<?php

use App\Http\Controllers\Admin\Auth\AdminController;
use App\Http\Controllers\Admin\Auth\ForgetPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\RegistrationController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin'], function () {
    Route::post('register', [RegistrationController::class, 'register']);
    Route::post('login', [LoginController::class, 'login'])->name('admin.login');
    Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword'])->name('admin.forgot-password');
    Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword'])->name('admin.reset-password');
    Route::post('refresh-token', [LoginController::class, 'refreshToken']);

    Route::group(['middleware' => 'auth:admin-api'], function () {
        Route::get('/test', function () {
            foreach (array_keys(config('auth.guards')) as $guard) {

                if (auth()->guard($guard)->check()) return $guard;
            }
            return response()->json(['success' => true, 'message' => 'You are authorized to access this data!'], 200);
        })->name('data');

        Route::post('logout', [LoginController::class, 'logout']);
        Route::post('approve-user/{user}', [AdminController::class, 'approveUser'])->name('admin.approve-user');

        //User
        Route::resource('user', UserController::class);
        Route::resource('category', CategoryController::class);
        Route::resource('brand', BrandController::class);
        Route::resource('size', SizeController::class);
        Route::resource('color', ColorController::class);
    //User
    Route::resource('user', UserController::class);
    Route::get('approved-user/{id}', [UserController::class , 'approvedUser']);

        //Plan
        Route::resource('plan', PlanController::class);


//    Route::resource('product', \App\Http\Controllers\ProductController::class);

    });
});


