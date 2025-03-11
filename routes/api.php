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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class , 'login'])->name('login');
/* ------- Public Route */


/* ------- Private Route */
Route::post('/logout', [AuthController::class , 'logout']);


Route::get('/me', [AuthController::class , 'me']);


//image profile upload
Route::post('/upload-profile', [AuthController::class , 'uploadProfileImage']);

//image profile upload

//advertisement
Route::apiResource('advertisements',AdvertisementController::class);

//advertisement

//category
Route::apiResource('category',CategoryController::class);

//category

//color
Route::apiResource('color',ColorController::class);

//color
//size
Route::apiResource('size', sizeController::class);

//size

//product
Route::apiResource('products', ProductController::class);

//product

//get product by category
Route::get('/product/category', [ProductController::class, 'getByCategory']);
//get product by category

//product variant
Route::apiResource('variants', \App\Http\Controllers\ProductVariantController::class);
//product variant
/* ------- Private Route */

