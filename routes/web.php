<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Website\WebsiteController;
use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Api\ProviderController;
;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!suba
|
*/

//Provider side login system
Route::post('provider_login', [LoginController::class, 'provider_login']);

//User side login system
Route::post('user_login', [LoginController::class, 'user_login']);
Route::post('verify_otp', [LoginController::class, 'verify_otp'])->name('verify_otp.login');


Route::match(['get', 'post'], 'admin/login', [AuthController::class, 'login'])->name('admin.login');

Route::middleware(['auth:admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');
    
    Route::post('punch-in', [AdminController::class, 'punchIn'])->name('admin.punchIn');
    Route::post('punch-out', [AdminController::class, 'punchOut'])->name('admin.punchOut');
    
    Route::get('attendances', [AdminController::class, 'attendances'])->name('admin.attendances');
    
    // Service Videos Routes
    Route::get('service-videos', [AdminController::class, 'service_videos'])->name('admin.service_videos');
    Route::match(['get', 'post'], 'service-videos/add', [AdminController::class, 'add_service_videos'])->name('admin.add_service_videos');
    Route::match(['get', 'post'], 'service-videos/{id}/edit', [AdminController::class, 'edit_service_videos'])->name('admin.edit_service_videos');
    Route::delete('service-videos/{id}/delete', [AdminController::class, 'delete_service_videos'])->name('admin.delete_service_videos');
    
        // Banner Routes
    Route::get('banners', [AdminController::class, 'banners'])->name('admin.banners');
    Route::match(['get', 'post'], 'banners/add', [AdminController::class, 'add_banners'])->name('admin.add_banners');
    Route::match(['get', 'post'], 'banners/{id}/edit', [AdminController::class, 'edit_banners'])->name('admin.edit_banners');
    Route::delete('banners/{id}/delete', [AdminController::class, 'delete_banners'])->name('admin.delete_banners');
    
    // Subadmin Routes
    Route::get('subadmins', [AdminController::class, 'subadmins'])->name('admin.subadmins');
    Route::match(['get', 'post'], 'subadmins/add', [AdminController::class, 'add_subadmins'])->name('admin.add_subadmins');
    Route::match(['get', 'post'], 'subadmins/{id}/edit', [AdminController::class, 'edit_subadmins'])->name('admin.edit_subadmins');
    Route::delete('subadmins/{id}/delete', [AdminController::class, 'delete_subadmins'])->name('admin.delete_subadmins');
    
    // Partners Data Routes
    Route::get('partnersData', [AdminController::class, 'partners_data'])->name('admin.partners_data');
    Route::match(['get', 'post'], 'partnersData/add', [AdminController::class, 'add_partners_data'])->name('admin.add_partners_data');
    Route::post('partners/update-status', [AdminController::class, 'updatePartnerStatus'])->name('admin.partners.updateStatus');
    
    Route::get('partnersContactList', [AdminController::class, 'partners_contactlist'])->name('admin.partners_contactlist');
    
    Route::get('about-us', [AdminController::class, 'about_us'])->name('admin.about_us');
    Route::post('about-us/update', [AdminController::class, 'update_about_us'])->name('admin.update_about_us');
    
    Route::get('privacy-policy', [AdminController::class, 'privacy_policy'])->name('admin.privacy_policy');
    Route::post('privacy-policy/update', [AdminController::class, 'update_privacy_policy'])->name('admin.update_privacy_policy');
    
    Route::get('contact-us', [AdminController::class, 'contact_us'])->name('admin.contact_us');
    Route::post('contact-us/update', [AdminController::class, 'update_contact_us'])->name('admin.update_contact_us');
    
    Route::get('terms-condition', [AdminController::class, 'terms_condition'])->name('admin.terms.index');
    Route::post('terms-condition/update', [AdminController::class, 'update_terms_condition'])->name('admin.terms.update');
    
    // Category Routes
    Route::get('categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::match(['get', 'post'], 'categories/add', [AdminController::class, 'add_categories'])->name('admin.add_categories');
    Route::match(['get', 'post'], 'categories/{id}/edit', [AdminController::class, 'edit_categories'])->name('admin.edit_categories');
    Route::delete('categories/{id}/delete', [AdminController::class, 'delete_categories'])->name('admin.delete_categories');
    
    // Plan Routes
    Route::get('plans/{category_id}', [AdminController::class, 'plans'])->name('admin.plans');
    Route::match(['get', 'post'], 'plans/{category_id}/add', [AdminController::class, 'add_plans'])->name('admin.add_plans');
    Route::match(['get', 'post'], 'plans/{category_id}/edit/{id}', [AdminController::class, 'edit_plans'])->name('admin.edit_plans');
    
    // Parts Routes
    Route::get('categories/{categoryId}/parts', [AdminController::class, 'parts'])->name('admin.parts');
    Route::match(['get', 'post'], 'categories/{categoryId}/parts/add', [AdminController::class, 'add_parts'])->name('admin.add_parts');
    Route::match(['get', 'post'], 'categories/{categoryId}/parts/{id}/edit', [AdminController::class, 'edit_parts'])->name('admin.edit_parts');
    Route::delete('categories/{categoryId}/parts/{id}/delete', [AdminController::class, 'delete_parts'])->name('admin.delete_parts');
    
    // Price Lists Routes
    Route::get('categories/{categoryId}/parts/{partId}/price-list', [AdminController::class, 'price_list'])->name('admin.price_list');
    Route::match(['get', 'post'], 'categories/{categoryId}/parts/{partId}/price-list/add', [AdminController::class, 'add_price_list'])->name('admin.add_price_list');
    Route::match(['get', 'post'], 'categories/{categoryId}/parts/{partId}/price-list/{id}/edit', [AdminController::class, 'edit_price_list'])->name('admin.edit_price_list');
    Route::delete('categories/{categoryId}/parts/{partId}/price-list/{id}/delete', [AdminController::class, 'delete_price_list'])->name('admin.delete_price_list');


    // Subcategory Routes
    Route::get('categories/{categoryId}/subcategories', [AdminController::class, 'subcategories'])->name('admin.subcategories');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/add', [AdminController::class, 'add_subcategory'])->name('admin.add_subcategory');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/{id}/edit', [AdminController::class, 'edit_subcategory'])->name('admin.edit_subcategory');
    Route::delete('categories/{categoryId}/subcategories/{id}/delete', [AdminController::class, 'delete_subcategory'])->name('admin.delete_subcategory');

    // Subsubcategory Routes
    Route::get('categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories', [AdminController::class, 'subsubcategories'])->name('admin.subsubcategories');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/add', [AdminController::class, 'add_subsubcategory'])->name('admin.add_subsubcategory');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{id}/edit', [AdminController::class, 'edit_subsubcategory'])->name('admin.edit_subsubcategory');
    Route::delete('categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{id}/delete', [AdminController::class, 'delete_subsubcategory'])->name('admin.delete_subsubcategory');

    // Service Routes
    Route::get('categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services', [AdminController::class, 'services'])->name('admin.services');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services/add', [AdminController::class, 'add_service'])->name('admin.add_service');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services/{id}/edit', [AdminController::class, 'edit_service'])->name('admin.edit_service');
    Route::delete('categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services/{id}/delete', [AdminController::class, 'delete_service'])->name('admin.delete_service');
    
    // How It Works Routes
    Route::get('categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services/{service_id}/how-it-works', [AdminController::class, 'how_it_works'])->name('admin.how_it_works');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services/{service_id}/how-it-works/add', [AdminController::class, 'add_how_it_works'])->name('admin.add_how_it_works');
    Route::match(['get', 'post'], 'categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services/{service_id}/how-it-works/{id}/edit', [AdminController::class, 'edit_how_it_works'])->name('admin.edit_how_it_works');
    Route::delete('categories/{categoryId}/subcategories/{subcategoryId}/subsubcategories/{subsubcategoryId}/services/{service_id}/how-it-works/{id}/delete', [AdminController::class, 'delete_how_it_works'])->name('admin.delete_how_it_works');


    Route::get('countries', [AdminController::class, 'countries'])->name('admin.countries');
    Route::get('countries/add', [AdminController::class, 'add_countries'])->name('admin.add_countries');
    Route::get('countries/edit/{id}', [AdminController::class, 'edit_countries'])->name('admin.edit_countries');
    Route::get('countries/delete/{id}', [AdminController::class, 'delete_countries'])->name('admin.delete_countries');

    //stete Route

    Route::get('state', [AdminController::class, 'states'])->name('admin.states');
    Route::get('state/add', [AdminController::class, 'add_states'])->name('admin.add_states');
    Route::get('state/edit/{id}', [AdminController::class, 'edit_states'])->name('admin.edit_states');
    Route::get('state/delete/{id}', [AdminController::class, 'delete_states'])->name('admin.delete_states');

    //Users Route

    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::match(['get', 'post'], 'users/add', [AdminController::class, 'add_users'])->name('admin.add_users');
    Route::match(['get', 'post'], 'users/{id}/edit', [AdminController::class, 'edit_users'])->name('admin.edit_users');
    Route::delete('users/{id}/delete', [AdminController::class, 'delete_users'])->name('admin.delete_users');
    
    Route::get('users/{userId}/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
    Route::match(['get', 'post'], 'users/{userId}/bookings/create-booking', [AdminController::class, 'create_booking'])->name('admin.create_booking');
    Route::get('/get-providers', [AdminController::class, 'getProviders'])->name('get.providers');

    Route::get('users/{userId}/addresses', [AdminController::class, 'addresses'])->name('admin.addresses');
    Route::match(['get', 'post'], 'users/{userId}/addresses/add-address', [AdminController::class, 'add_address'])->name('admin.add_address');


    Route::get('/get-subcategories/{categoryId}', [AdminController::class, 'getSubcategories'])->name('admin.getSubcategories');
    Route::get('/get-sub-subcategories/{categoryId}/{subcategoryId}', [AdminController::class, 'getSubSubcategories'])->name('admin.getSubSubcategories');
    Route::get('/get-services/{categoryId}/{subcategoryId}/{subSubcategoryId}', [AdminController::class, 'getServices'])->name('admin.getServices');
    Route::get('/get-daily-slots', [AdminController::class, 'getDailySlots']);
    
    //Provides Route
    Route::get('user-details/{id}', [AdminController::class, 'user_details'])->name('admin.user_details');
    Route::post('user-block/{id}', [AdminController::class, 'user_block']);
    Route::get('providers', [AdminController::class, 'providers'])->name('admin.providers');
    Route::match(['get', 'post'],'providers/add', [AdminController::class, 'add_providers'])->name('admin.add_providers');
    Route::match(['get', 'post'], 'providers/{id}/edit', [AdminController::class, 'edit_providers'])->name('admin.edit_providers');
    Route::delete('providers/{id}/delete', [AdminController::class, 'delete_providers'])->name('admin.delete_providers');

    Route::get('/get-zones/{providerId}', [AdminController::class, 'getZones'])->name('get_zones');
    Route::post('/assign-zones', [AdminController::class, 'assignZones'])->name('admin.assign_zones');


    Route::get('/get-plans/{id}/{type}', [AdminController::class, 'getPlans']);
    Route::post('/activate-security/{id}/{planId}', [AdminController::class, 'activateSecurity']);

    // dynamic state & city selection

    Route::get('/get-states/{country_id}', [AdminController::class, 'getStates'])->name('get.states');
    Route::get('/get-cities/{state_id}', [AdminController::class, 'getCities'])->name('get.cities');
    
    // sub_subCategory Route
    // Route::get('/sub-sub-categories/{subcategory_id}', [AdminController::class, 'subSubCategory'])->name('admin.subSubCategories');
    // Route::match(['get', 'post'], '/admin/add-sub-sub-category/{subcategory_id}', [AdminController::class, 'addSubSubCategory'])->name('admin.addSubSubCategory');
    // Route::match(['get', 'post'], 'admin/edit-subsubcategory/{subcategory_id}/{id}', [AdminController::class, 'editSubSubCategory']) ->name('admin.edit_subsubcategory');
    // Route::delete('/delete-subsubcategory/{subcategory_id}/{id}', [AdminController::class, 'deleteSubSubCategory'])->name('admin.delete_subsubcategory');
   
    // Service
    // Route::get('service/{category_id}/{subcategory_id}/{id}', [AdminController::class, 'service'])->name('admin.service');
    // Route::match(['get', 'post'], '/admin/add_service/{category_id}/{subcategory_id}/{sub_subcategory_id}', [AdminController::class, 'addService'])->name('admin.add_service');
    // Route::match(['get', 'post'], '/admin/edit_service/{category_id}/{subcategory_id}/{sub_subcategory_id}/{service_id}', [AdminController::class, 'editService'])->name('admin.edit_service');
    // Route::delete('/admin/service/{id}', [AdminController::class, 'deleteService'])->name('admin.delete_service');
 

    //transaction
    Route::get('transaction',[AdminController::class,'transaction'])->name('admin.transaction');
    
     //zone
    Route::get('zone', [AdminController::class, 'zone'])->name('admin.zones');
    Route::match(['get', 'post'], 'zone/add', [AdminController::class, 'add_zone'])->name('admin.add_zone');
    Route::get('zone/edit/{id}', [AdminController::class, 'edit_zone'])->name('admin.edit_zone');
    Route::post('zone/update/{id}', [AdminController::class, 'update_zone'])->name('admin.update_zone');

    // Route::match(['get', 'post'], 'assign_provider', [AdminController::class, 'assign_provider'])->name('admin.assign_provider');
    // Route::get('get_providers/{zone_id}', [AdminController::class, 'get_providers'])->name('admin.get_providers');
    
    Route::get('all-bookings', [AdminController::class, 'all_bookings'])->name('admin.all_bookings');
    Route::post('update-booking-field', [AdminController::class, 'updateField'])->name('admin.update_booking_field');
    Route::post('complaints/store-direct', [AdminController::class, 'storeFromBooking'])->name('admin.complaints.store_direct');
    Route::get('booking-detail/{id}', [AdminController::class, 'booking_detail'])->name('admin.booking_detail');
    Route::get('complaints', [AdminController::class, 'complaints'])->name('admin.complaints');
    Route::post('complaints/{complaint}/resolve', [AdminController::class, 'resolve'])->name('admin.complaints.resolve');
    Route::post('bookings-cancel', [AdminController::class, 'booking_cancel'])->name('admin.booking.cancel');
    Route::post('booking-assign-zone', [AdminController::class, 'assignZoneToBooking'])->name('admin.booking_assign_zone');
    
    Route::get('/bookings/export-csv', [AdminController::class, 'exportCsv'])->name('admin.bookings.export_csv');
    Route::post('change-status', [AdminController::class, 'changeStatus'])->name('admin.change-status');

    // Route For Rating and Review
    Route::get('rating_review', [AdminController::class, 'ratingReview'])->name('admin.rating_review');
    Route::match(['get', 'post'], 'add/rating_review', [AdminController::class, 'addRatingReview'])->name('admin.add_rating_review');
    Route::match(['get', 'put', 'post'], '/edit/rating_review/{id}', [AdminController::class, 'editRatingReview'])->name('admin.edit_rating_review');
    Route::delete('/delete/rating_review/{id}', [AdminController::class, 'deleteRatingReview'])->name('admin.delete_rating_review');
    
    // Route For All Report
    Route::get('all-report', [AdminController::class, 'allReport'])->name('admin.all-report');

    Route::get('provider-report', [AdminController::class, 'providerReport'])->name('admin.provider-report');


    // Route For Quotation
    Route::get('getUserDataByBookingId/{booking_id}', [AdminController::class, 'getUserDataByBookingId'])->name('admin.getUserDataByBookingId');

    Route::get('quotations', [AdminController::class, 'quotations'])->name('admin.quotations');
    Route::match(['get', 'post'], 'add/quotations', [AdminController::class, 'addQuotations'])->name('admin.add-quotations');
    Route::match(['get', 'put', 'post'], '/edit/quotations/{id}', [AdminController::class, 'editQuotations'])->name('admin.edit-quotations');
    Route::delete('/delete/quotations/{id}', [AdminController::class, 'deleteQuotations'])->name('admin.delete-quotations');

    Route::get('/view/quotations/{id}', [AdminController::class, 'viewQuotations'])->name('admin.view-quotations');

    Route::get('/download/quotations/{id}', [AdminController::class, 'downloadQuotations'])->name('admin.download-quotations');

 
});

Route::get('terms-and-conditions', [WebsiteController::class, 'terms_conditions']);
Route::get('contact-us', [WebsiteController::class, 'contact_us']);
Route::get('refund-policy', [WebsiteController::class, 'refund_policy']);
Route::get('privacy-policy', [WebsiteController::class, 'privacy_policy']);