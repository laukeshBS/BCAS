<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Cms\EventController;
use App\Http\Controllers\Cms\SlideController;
use App\Http\Controllers\Cms\NoticeController;
use App\Http\Controllers\Cms\SliderController;
use App\Http\Controllers\Cms\TenderController;
use App\Http\Controllers\Cms\VisitorController;
use App\Http\Controllers\Cms\CircularController;
use App\Http\Controllers\Cms\MenuController as menus;
use App\Http\Controllers\Cms\ActandpoliciesController;
use App\Http\Controllers\Cms\CommonController as Common;
use App\Http\Controllers\Cms\LanguageController as lang;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/', function () {
    echo "api"; die();
 });
Route::get('/clear-config', function () {
    Artisan::call('config:clear');
    return 'Config cache cleared successfully.';
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'Cache cleared successfully.';
});

Route::middleware('cors')->group(function () {
    Route::controller(lang::class)->group(function(){
        Route::post('langlist','index');
       
    });
    Route::controller(menus::class)->group(function(){
        Route::post('menulist','index');
        Route::post('menu/lang_slgus_wise','lang_slgus_wise');
      
    });
    Route::controller(Common::class)->group(function(){
        Route::post('common_title','index');
      
    });
    Route::controller(VisitorController::class)->group(function(){
        Route::post('/visitor/count','getVisitorCount');
        Route::post('/visitor/increment','incrementVisitorCount');
      
    });
    Route::controller(SliderController::class)->group(function(){
        Route::post('slider-by-slug','slider_by_slug');
        Route::post('slider-store','store_slider_api');
      
    });
    Route::controller(SlideController::class)->group(function(){
        Route::post('slide-store','store_slide_api');
      
    });
    Route::controller(ActandpoliciesController::class)->group(function(){
        Route::post('acts-and-policies-list','data_api');
        Route::get('acts-and-policies-list-by-id/{id}','get_data_by_id_api');
        // Route::post('acts-and-policies-store','store_api')->name('cms.acts.and.policies.store');
      
    });
    Route::controller(EventController::class)->group(function(){
        Route::post('event-list','event_list');
        Route::post('event-list-for-homepage','event_list_for_homepage');
        Route::post('event-store','event_store');
      
    });
    Route::controller(CircularController::class)->group(function(){
        Route::post('circular-list','circular_list');
        Route::post('circular-list-for-homepage','circular_list_for_homepage');
        Route::post('circular-store','circular_store');
      
    });
    Route::controller(NoticeController::class)->group(function(){
        Route::post('notice-list','notice_list');
        Route::post('notice-list-for-homepage','notice_list_for_homepage');
        Route::post('notice-store','notice_store');
      
    });
    Route::controller(TenderController::class)->group(function(){
        Route::post('tender-list','data');
        Route::get('tender-details/{id}','data_by_id');
        Route::post('tender-store','tender_store');
      
    });
});