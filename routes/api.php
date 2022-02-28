<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\APIController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\EmailVerificationController;


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




Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);
Route::post('/forgot-password',[ForgotPasswordController::class,'forgotPassword'])->middleware('guest')->name('password.request');
Route::post('/reset-password',[ForgotPasswordController::class,'reset'])->middleware('guest')->name('password.reset');

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('logout', [ApiController::class, 'logout']);
    Route::get('get_user', [ApiController::class, 'get_user']);
    Route::get('/verify-email/{id}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::get('/email/verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    Route::get('/discover', [PostController::class, 'index']);
    Route::get('/UserFeeds', [PostController::class, 'show']);
    Route::post('/createpost', [PostController::class, 'store']);
    Route::post('/update/{post}',  [PostController::class, 'update']);
    Route::delete('/delete/{post}',  [PostController::class, 'destroy']);
    Route::post('/createfollower', [PostController::class,'createfollower']);
    Route::post('/post/like', [PostController::class,'likePost']);
    Route::get('/post/likes/{req}', [PostController::class,'getLikes']);
});
