<?php

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Cms\FormController;
use App\Http\Controllers\Cms\EventController;
use App\Http\Controllers\Cms\SlideController;
use App\Http\Controllers\Cms\NoticeController;
use App\Http\Controllers\Cms\RegionController;
use App\Http\Controllers\Cms\SliderController;
use App\Http\Controllers\Cms\TenderController;
use App\Http\Controllers\Cms\ContactController;
use App\Http\Controllers\Cms\VacancyController;
use App\Http\Controllers\Cms\VisitorController;
use App\Http\Controllers\Cms\CircularController;
use App\Http\Controllers\Cms\DivisionController;
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
        Route::post('menu/lang_pid_wise','lang_pid_wise');
      
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
        Route::post('acts-and-policies-list','data');
        Route::get('acts-and-policies-list-by-id/{id}','data_by_id');
        Route::post('acts-and-policies-store','store');
        Route::post('acts-and-policies-update/{id}','update');
        Route::delete('acts-and-policies-delete/{id}','delete');
      
    });
    Route::controller(EventController::class)->group(function(){
        Route::post('event-list','event_list');
        Route::post('event-list-for-homepage','event_list_for_homepage');
        Route::post('event-list','data');
        Route::get('event-list-by-id/{id}','data_by_id');
        Route::post('event-store','store');
        Route::post('event-update/{id}','update');
        Route::delete('event-delete/{id}','delete');
      
    });
    Route::controller(CircularController::class)->group(function(){
        Route::post('circular-list','circular_list');
        Route::post('circular-list-for-homepage','circular_list_for_homepage');
        Route::post('circular-list','data');
        Route::get('circular-list-by-id/{id}','data_by_id');
        Route::post('circular-store','store');
        Route::post('circular-update/{id}','update');
        Route::delete('circular-delete/{id}','delete');
      
    });
    Route::controller(NoticeController::class)->group(function(){
        Route::post('notice-list','notice_list');
        Route::post('notice-list-for-homepage','notice_list_for_homepage');
        Route::post('notice-list','data');
        Route::get('notice-list-by-id/{id}','data_by_id');
        Route::post('notice-store','store');
        Route::post('notice-update/{id}','update');
        Route::delete('notice-delete/{id}','delete');
      
    });
    Route::controller(TenderController::class)->group(function(){
        Route::post('tender-list','data');
        Route::get('tender-details/{id}','data_by_id');
        Route::post('tender-list','data');
        Route::get('tender-list-by-id/{id}','data_by_id');
        Route::post('tender-store','store');
        Route::post('tender-update/{id}','update');
        Route::delete('tender-delete/{id}','delete');
      
    });
    Route::controller(FormController::class)->group(function(){
        Route::post('form-list','data');
        Route::get('form-list-by-id/{id}','data_by_id');
        Route::post('form-store','store');
        Route::post('form-update/{id}','update');
        Route::delete('form-delete/{id}','delete');
      
    });
    Route::controller(VacancyController::class)->group(function(){
        Route::post('vacancy-list','data');
        Route::get('vacancy-list-by-id/{id}','data_by_id');
        Route::post('vacancy-store','store');
        Route::post('vacancy-update/{id}','update');
        Route::delete('vacancy-delete/{id}','delete');
      
    });
    Route::controller(ContactController::class)->group(function(){
        Route::post('contact-list','data');
        Route::get('contact-list-by-id/{id}','data_by_id');
        Route::post('contact-store','store');
        Route::post('contact-update/{id}','update');
        Route::delete('contact-delete/{id}','delete');
      
    });
    Route::controller(DivisionController::class)->group(function(){
        Route::post('division-list','data');
        Route::get('division-list-by-id/{id}','data_by_id');
        Route::post('division-store','store');
        Route::post('division-update/{id}','update');
        Route::delete('division-delete/{id}','delete');
      
    });
    Route::controller(RegionController::class)->group(function(){
        Route::post('region-list','data');
        Route::get('region-list-by-id/{id}','data_by_id');
        Route::post('region-store','store');
        Route::post('region-update/{id}','update');
        Route::delete('region-delete/{id}','delete');
      
    });
});