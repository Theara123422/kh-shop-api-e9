<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\advertisement\AdvertisementController;
use App\Http\Controllers\social\SocialPlatformController;
use App\Http\Controllers\feedback\FeedbackController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\color\ColorController;
use App\Http\Controllers\country\CountryController;
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

// countries
Route::get('/countries', [CountryController::class, 'getCountries']);
Route::get('/cities/{country}', [CountryController::class, 'getCities']);
// countries

/* ------- Private Route */
Route::post('/logout', [AuthController::class , 'logout']);


Route::get('/me', [AuthController::class , 'me']);


//image profile upload
Route::post('/upload-profile', [AuthController::class , 'uploadProfileImage']);

//image profile upload


//social platforms
Route::apiResource('social-platforms',SocialPlatformController::class);

//social platforms

//feedback
Route::apiResource('feedback',FeedbackController::class);
//feedback

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


Route::get('/product/latest', [ProductController::class, 'latestProducts']);
Route::get('/product/promotions', [ProductController::class, 'promotionalProducts']);
Route::get('/product/top-rated', [ProductController::class, 'topRatedProducts']);
//get product by category

//product variant
// Route::apiResource('variants', \App\Http\Controllers\ProductVariantController::class);
//product variant
/* ------- Private Route */

