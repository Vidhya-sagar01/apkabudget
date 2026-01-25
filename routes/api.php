<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommonController;

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

// API route example
Route::get('countries', [ProviderController::class, 'countries']);
Route::post('states', [ProviderController::class, 'states']);
Route::post('cities', [ProviderController::class, 'cities']);
Route::get('zones', [ProviderController::class, 'zones']);

Route::post('test_api', [CommonController::class, 'test_api']);

//Provider side login system
Route::post('provider_register', [AuthController::class, 'provider_register']);
Route::post('provider_login', [AuthController::class, 'provider_login']);

//User side login system
Route::post('user_login', [AuthController::class, 'user_login']);
Route::post('verify_otp', [AuthController::class, 'verify_otp']);

Route::get('categories', [CommonController::class, 'categories']);
Route::get('sub_categories', [CommonController::class, 'subCategories']);
Route::get('sub_sub_categories', [CommonController::class, 'SubCategories']);


Route::post('terms_conditions', [ProviderController::class, 'terms_conditions']);

// Route::group(['middleware' => 'auth:sanctum'], function () {
Route::middleware(['api.auth'])->group(function () {
    
    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('delete-account', [AuthController::class, 'deleteAccount']);

    //Provider side api's
    Route::get('identity_types', [ProviderController::class, 'identity_types']);
    Route::post('payment_status', [ProviderController::class, 'payment_status']);// payment karne ke baad ye api hit hogi
    Route::get('dashboard', [ProviderController::class, 'dashboard']);
    Route::post('bookings', [ProviderController::class, 'bookings']); //provider side all booking list
    Route::post('booking_detail', [ProviderController::class, 'booking_detail']);
    Route::post('booking_action', [ProviderController::class, 'booking_action']);
    Route::post('plans', [ProviderController::class, 'plans']);
    Route::post('add_service', [ProviderController::class, 'add_service']); //Booking edit add extra services
    // Route::post('added_services', [ProviderController::class, 'added_services']); //Extra added service list
    Route::post('invoice', [ProviderController::class, 'invoice']);
    Route::post('upload-contacts', [ProviderController::class, 'upload_contacts']);
    Route::post('paint-category', [ProviderController::class, 'paint_category']);
    Route::post('paint-name', [ProviderController::class, 'paint_name']);
    
    Route::post('phonepe-initiate', [ProviderController::class, 'initiate']);
    
    //User side Api's
    Route::post('save_location', [UserController::class, 'save_location']);
    Route::post('sub_categories', [UserController::class, 'sub_categories']);
    Route::post('services', [UserController::class, 'services']); //sub_subcategories and services and view_cart three includes
    Route::post('how_it_work', [UserController::class, 'how_it_work']);
    Route::post('rate_card', [UserController::class, 'rate_card']);
    Route::post('add-to-cart', [UserController::class, 'addToCart']); // services add in cart
    Route::post('add_address', [UserController::class, 'add_address']); //add address
    Route::get('addresses', [UserController::class, 'addresses']); // address list
    Route::post('daily-slots', [UserController::class, 'getDailySlots']); // View Slots
    Route::post('checkout', [UserController::class, 'checkout']); // check out cart item subctegory wise
    Route::post('paymentstatus', [UserController::class, 'paymentstatus']);// check payment after booking
    Route::get('my_bookings', [UserController::class, 'my_bookings']); //booking list
    Route::post('submitcomplaints', [UserController::class, 'submitComplaint']);
    Route::get('service_videos', [UserController::class, 'service_videos']); //service videos
    Route::get('banners', [UserController::class, 'banners']); //Banner
    Route::post('price_list', [UserController::class, 'price_list']); //Price Lists
    Route::get('user/terms_conditions', [UserController::class, 'user_terms_conditions']);
    
    Route::get('home', [UserController::class, 'home']);

    //Common Api's
    Route::get('notifications', [CommonController::class, 'notifications']); // all notiication list
    Route::get('transaction_history', [CommonController::class, 'transaction_history']);
    Route::get('profile', [CommonController::class, 'profile']);
    Route::post('edit_profile', [CommonController::class, 'edit_profile']);
    Route::get('privacy_policy', [CommonController::class, 'privacy_policy']);
    Route::get('refund-policy', [CommonController::class, 'refund_policy']);
    Route::get('about_us', [CommonController::class, 'about_us']);
    Route::get('contact_us', [CommonController::class, 'contact_us']);
    Route::get('social_profiles', [CommonController::class, 'social_profiles']);

});

Route::post('send-notification', [UserController::class, 'send_notification']);// check payment
Route::post('/webhook', [CommonController::class, 'handleWebhook']);
Route::post('/testpayment', [CommonController::class, 'testPayment']);

Route::get('phonepe-callback', [ProviderController::class, 'handleCallback']);

