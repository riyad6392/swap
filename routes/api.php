<?php

use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanSubscriptionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SwapController;
use App\Http\Controllers\SwapExchangeDetailsController;
use App\Http\Controllers\SwapRequestDetailsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgetPasswordController;

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


Route::post('register', [RegistrationController::class, 'register']);
Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('forget-password', [ForgetPasswordController::class, 'forgetPassword']);
Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword']);
Route::post('refresh-token', [LoginController::class, 'getRefreshToken']);



Route::group(['middleware' => 'auth:api'], function () {

    Route::get('/test' , function(){
        return response()->json(['success' => true, 'message' => 'You are authorized to access this data!'], 200);
    });

    Route::post('logout', [LoginController::class, 'logout']);

    Route::group(['middleware' => 'check.subscription'], function () {

        Route::get('subscribe-check', function () {
            return response()->json(['success' => true, 'message' => 'You are subscribed to a plan.'], 200);
        });

        //Inventory

        Route::resource('category', CategoryController::class);
        Route::resource('product', ProductController::class);
        Route::resource('plan', PlanController::class);

        //rating
        Route::apiResource('ratings', RatingController::class);
        Route::get('ratings/given-to-me', [RatingController::class, 'ratingsGivenToMe']);
        Route::get('ratings/given-by-me', [RatingController::class, 'ratingsGivenByMe']);

        //swap

        Route::resource('swap', SwapController::class);
        Route::resource('swap-request-details', SwapRequestDetailsController::class);
        Route::resource('swap-exchange-details', SwapExchangeDetailsController::class);
        Route::get('swap-approve/{id}', [SwapController::class, 'approve']);
        Route::get('swap-decline/{id}', [SwapController::class, 'decline']);

//        Broadcast::routes();

        Route::get('messages', [MessageController::class , 'index']);
        Route::post('prepare-conversation', [MessageController::class , 'prepareConversation']);
        Route::post('send-messages', [MessageController::class , 'sendMessages']);

        //Notification
        Route::get('notifications', [NotificationController::class , 'index']);
        Route::get('notification-show/{id}', [NotificationController::class , 'show']);
        Route::post('mark-as-read', [NotificationController::class , 'markAllAsRead']);
        Route::post('mark-as-unread', [NotificationController::class , 'markAllAsUnRead']);


    });

    //Subscription

    Route::post('plan-subscription', [PlanSubscriptionController::class , 'subscribe']);
    Route::delete('cancel-subscription/{id}', [PlanSubscriptionController::class , 'cancelSubscription']);

    //Payment Method

    Route::post('payment-method', [PaymentMethodController::class, 'store']);
    Route::put('update-payment-method', [PaymentMethodController::class, 'update']);
    Route::delete('delete-payment-method/{payment_method_id}', [PaymentMethodController::class, 'destroy']);



});

require __DIR__.'/admin/admin_api.php';
