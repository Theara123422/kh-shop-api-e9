<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\advertisement\AdvertisementController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\color\ColorController;
use App\Http\Controllers\Size\sizeController;
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
Route::get('/', function(){
    return response()->json([
        'msg' => "123"
    ]);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class , 'login'])->name('login');

/* ------- Public Route */


/* ------- Private Route */
Route::post('/logout', [AuthController::class , 'logout'])
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);

Route::get('/me', [AuthController::class , 'me'])
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);

//advertisement
Route::apiResource('advertisements',AdvertisementController::class)
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);
//advertisement

//category
Route::apiResource('category',CategoryController::class)
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);
//category

<<<<<<< HEAD
//color
Route::apiResource('color',ColorController::class);
//color
//size
Route::apiResource('size',sizeController::class);
//size
git ad
/* ------- Private Route */
=======
//image profile upload
Route::post('/upload-profile', [AuthController::class , 'uploadProfileImage'])
->middleware([
    'auth.jwt'
]);
//image profile upload
/* ------- Private Route */
>>>>>>> 1a5374aa2e31ec5d284baf6c5e371ba2a2070cfe
