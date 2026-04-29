<?php

use App\Http\Controllers\API\Customers\AdsAPIController;
use App\Http\Controllers\API\Customers\AuthController;
use App\Http\Controllers\API\Customers\BookingAPIController;
use App\Http\Controllers\API\Customers\CustomerAPIController;
use App\Http\Controllers\API\Customers\EntityAPIContorller;
use App\Http\Controllers\API\Customers\MenuAPIController;
use App\Http\Controllers\API\Customers\MenuCategoryAPIController;
use App\Http\Controllers\API\Customers\PackageAPIController;
use App\Http\Controllers\Customers\FoodOrderAPIController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\HomeController;
use Illuminate\Support\Facades\Auth;

// Route::middleware('auth:api')->group(function () {
Route::controller(HomeController::class)->group(function () {
    Route::get('home_category_list', 'getHomeCategoryList');
    Route::get('home_menu_list', 'getHomeMenuList');
});
Route::post('/customers/initial_register', [AuthController::class, 'initialRegister']);
Route::post('/customers/register', [AuthController::class, 'register']);
Route::post('/customers/login', [AuthController::class, 'login']);
Route::post('/customers/forgot_password', [AuthController::class, 'forgotPassword']);
Route::post('/customers/reset_password', [AuthController::class, 'changeForgetPassword']);

Route::middleware('auth:customer_api')->group(function () {

    Route::post('/customers/logout', [AuthController::class, 'logout']);
    Route::post('/customers/change_password', [AuthController::class, 'changePassword']);

    // Route::post('/user_app/change_phone_otp', [CustomerAPIController::class, 'changePhoneNumberOTP']);
    // Route::post('/user_app/change_phone_number', [CustomerAPIController::class, 'changePhoneNumber']);


    // Route::get('/user_app/profile', [CustomerAPIController::class, 'getCustomerDataByUserApp']);
    // Route::post('/user_app/user_profile/edit', [CustomerAPIController::class, 'customerProfileEdit']);

    // Route::get('/user_app/user_addresses', [CustomerAPIController::class, 'getCustomerAddressByUserApp']);
    // Route::post('/user_app/user_addresses/{id}', [CustomerAPIController::class, 'updateCustomerAddress']);
    // Route::post('/user_app/user_addresses', [CustomerAPIController::class, 'createCustomerAddress']);
    // Route::post('/user_app/default_addresses/{id}', [CustomerAPIController::class, 'defaultCustomerAddress']);
    // Route::delete('/user_app/user_addresses/{id}', [CustomerAPIController::class, 'deleteCustomerAddress']);

    // Route::get('/user_app/bookings', [BookingAPIController::class, 'listAllBookings']);
    // Route::post('/user_app/bookings', [BookingAPIController::class, 'createBooking']);
    // Route::get('/user_app/room_lists', [EntityAPIContorller::class, 'roomList']);
    // Route::post('/user_app/food_orders', [FoodOrderAPIController::class, 'createOrder']);

});

// Route::get('/user_app/ads', [AdsAPIController::class, 'getAdsByUserApp']);
// Route::get('/user_app/menu_categories', [MenuCategoryAPIController::class, 'getMenuCategoriesbyUserApp']);
// Route::get('/user_app/menus', [MenuAPIController::class, 'listMenuData']);
// Route::get('/user_app/menu_categories/{id}/menus', [MenuAPIController::class, 'categoryMenuByUserApp']);
// Route::get('/user_app/packages', [PackageAPIController::class, 'getPackage']);


// });
