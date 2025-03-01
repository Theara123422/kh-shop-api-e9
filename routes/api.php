<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\advertisement\AdvertisementController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\color\ColorController;
use App\Http\Controllers\product\ProductController;
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

//image profile upload
Route::post('/upload-profile', [AuthController::class , 'uploadProfileImage'])
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);
//image profile upload

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

//color
Route::apiResource('color',ColorController::class)
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);
//color
//size
Route::apiResource('size', sizeController::class)
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);
//size

//product
Route::apiResource('product', ProductController::class)
->middleware([
    'auth.jwt',
    'jwt.refresh'
]);
//product
/* ------- Private Route */

