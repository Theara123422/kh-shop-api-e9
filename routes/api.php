<?php

use App\Http\Controllers\auth\AuthController;
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

/* ------- Public Route */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class , 'login'])->name('login');
/* ------- Public Route */


/* ------- Private Route */
Route::post('/logout', [AuthController::class , 'logout'])
->middleware([
    'auth:api',
    'jwt.refresh'
]);

Route::get('/me', [AuthController::class , 'me'])
->middleware([
    'auth:api',
    'jwt.refresh'
]);
/* ------- Private Route */
