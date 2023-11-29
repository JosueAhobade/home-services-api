<?php
namespace App\Http\Controllers\Api\Auth;

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


Route::post('/auth/register',[AuthController::class,'register']);

Route::post('/auth/login',[AuthController::class,'login']);

Route::post('/auth/verify_email',[AuthController::class,'verifyUserEmail']);

Route::post('/auth/resend_link',[AuthController::class,'resendEmailVerificationLink']);

Route::post('/auth/reset_password',[AuthController::class,'resetPassword']);

Route::middleware(['auth:api','verified'])->post('/auth/reset_password', [AuthController::class,'resetPassword']);
