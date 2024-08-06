<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cms\EventController;
use App\Http\Controllers\Cms\SlideController;
use App\Http\Controllers\Cms\NoticeController;
use App\Http\Controllers\Cms\SliderController;
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
        Route::post('slider-by-slug','slider_by_slug')->name('cms.slider.by.slug');
        Route::post('slider-store','store_slider_api')->name('cms.store.slider.api');
      
    });
    Route::controller(SlideController::class)->group(function(){
        Route::post('slide-store','store_slide_api')->name('cms.store.slide.api');
      
    });
    Route::controller(ActandpoliciesController::class)->group(function(){
        Route::post('acts-and-policies-list','data_api')->name('cms.acts.and.policies.list');
        Route::get('acts-and-policies-list-by-id/{id}','get_data_by_id_api')->name('cms.acts.and.policies.list.by.id');
        // Route::post('acts-and-policies-store','store_api')->name('cms.acts.and.policies.store');
      
    });
    Route::controller(EventController::class)->group(function(){
        Route::post('event-list','event_list')->name('cms.event.list');
        Route::post('event-list-for-homepage','event_list_for_homepage')->name('cms.event.list.for.homepage');
        Route::post('event-store','event_store')->name('cms.event.store');
      
    });
    Route::controller(CircularController::class)->group(function(){
        Route::post('circular-list','circular_list')->name('cms.circular.list');
        Route::post('circular-list-for-homepage','circular_list_for_homepage')->name('cms.circular.list.for.homepage');
        Route::post('circular-store','circular_store')->name('cms.circular.store');
      
    });
    Route::controller(NoticeController::class)->group(function(){
        Route::post('notice-list','notice_list')->name('cms.notice.list');
        Route::post('notice-list-for-homepage','notice_list_for_homepage')->name('cms.notice.list.for.homepage');
        Route::post('notice-store','notice_store')->name('cms.notice.store');
      
    });
});