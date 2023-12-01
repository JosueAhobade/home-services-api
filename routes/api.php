<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/auth/register',[App\Http\Controllers\Api\Auth\AuthController::class,'register']);

Route::post('/auth/login',[App\Http\Controllers\Api\Auth\AuthController::class,'login']);

Route::post('/auth/verify_email',[App\Http\Controllers\Api\Auth\AuthController::class,'verifyUserEmail']);

Route::post('/auth/resend_link',[App\Http\Controllers\Api\Auth\AuthController::class,'resendEmailVerificationLink']);

Route::post('/auth/reset_password',[App\Http\Controllers\Api\Auth\AuthController::class,'resetPassword']);

Route::middleware(['auth:api','verified'])->post('/auth/reset_password', [App\Http\Controllers\Api\Auth\AuthController::class,'resetPassword']);

Route::middleware(['auth:api','verified'])->post('/addchild', [App\Http\Controllers\Api\ChildController::class,'addChild']);

Route::middleware(['auth:api','verified'])->post('/editchild{child}', [App\Http\Controllers\Api\ChildController::class,'editChild']);

Route::middleware(['auth:api','verified'])->post('/deletechild{child}', [App\Http\Controllers\Api\ChildController::class,'deleteChild']);

Route::middleware(['auth:api','verified'])->post('/createconsultation', [App\Http\Controllers\ConsultationController::class,'store']);


