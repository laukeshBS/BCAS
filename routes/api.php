<?php

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
use App\Http\Controllers\Cms\AirlinesController;
use App\Http\Controllers\Cms\CircularController;
use App\Http\Controllers\Cms\DivisionController;
use App\Http\Controllers\Cms\OpsSecurityController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Cms\OpsiSecurityController;
use App\Http\Controllers\Cms\MenuController as menus;
use App\Http\Controllers\Cms\ActandpoliciesController;
use App\Http\Controllers\Cms\CateringCompanyController;
use App\Http\Controllers\Cms\WorkingAirportsController;
use App\Http\Controllers\Cms\QuarterlyReportOnlineiiFormsController;
use App\Http\Controllers\Cms\CommonController as Common;
use App\Http\Controllers\Cms\LanguageController as lang;
use App\Http\Controllers\Cms\Common\CommonTitleController;
use App\Http\Controllers\Cms\PermittedProhibitedController;
use App\Http\Controllers\Cms\AvsecTrainingCalendarController;
use App\Http\Controllers\Cms\QuarterlyReportOnlineFormsController;
use App\Http\Controllers\Cms\QuarterlyReportOnlineiiFormsController;
use App\Http\Controllers\Cms\FeedbackController as FeedbackController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/


Route::get('/', function () {
    return "api";
});

Route::get('/clear-config', function () {
    Artisan::call('config:clear');
    return 'Config cache cleared successfully.';
});

Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return 'Cache cleared successfully.';
});

// Public Routes
Route::post('login', [LoginController::class, 'login']);
Route::middleware(['cors', 'throttle:60,1'])->group(function () {
    Route::controller(FeedbackController::class)->group(function () {
       Route::any('submit_feedback', 'store');
    });
    Route::controller(QuarterlyReportOnlineFormsController::class)->group(function () {
        Route::post('quarterly-report-online', 'store');
    });

    Route::controller(QuarterlyReportOnlineiiFormsController::class)->group(function () {
        Route::post('quarterly-report2-online', 'store');
    });
});
Route::middleware(['cors'])->group(function () {
    Route::get('/csrf-token', function () {
        return response()->json(['csrfToken' => csrf_token()]);
    });

    Route::controller(lang::class)->group(function () {
        Route::post('langlist', 'index');
    });

    Route::controller(menus::class)->group(function () {
        Route::post('menu-list', 'menu_list');
        Route::post('menu/lang_slugs_wise', 'lang_slugs_wise');
        Route::post('menu/lang_pid_wise', 'lang_pid_wise');
    });

    Route::controller(Common::class)->group(function () {
        Route::post('common_title', 'index');
    });
   Route::controller(VisitorController::class)->group(function () {
        Route::post('/visitor/count', 'getVisitorCount');
        Route::post('/visitor/increment', 'incrementVisitorCount');
    });

    Route::controller(menus::class)->group(function(){
        Route::post('menulist','data');
        Route::get('menu-by-id/{id}','data_by_id');
        Route::post('menu-store','store');
        Route::post('menu-update/{id}','update');
        Route::delete('menu-delete/{id}','delete');


    Route::controller(SliderController::class)->group(function () {
        Route::post('slider-by-slug', 'slider_by_slug');
    });

    Route::controller(ActandpoliciesController::class)->group(function () {
        Route::post('acts-and-policies-list', 'data');
        Route::get('acts-and-policies-list-by-id/{id}', 'data_by_id');
    });
    Route::controller(AvsecTrainingCalendarController::class)->group(function(){
        Route::post('avsecTraining-list-aprpoved','avsecTrainingCalendar_list_approved');
     });
     Route::controller(QuarterlyReportOnlineFormsController::class)->group(function(){
         Route::post('quarterly-report-online','store');
     });
     Route::controller(QuarterlyReportOnlineiiFormsController::class)->group(function(){
         Route::post('quarterly-report2-online','store');
     });
    Route::controller(EventController::class)->group(function () {
        Route::post('event-list', 'event_list');
        Route::post('event-list','data');
        Route::post('event-list-for-homepage', 'event_list_for_homepage');
        Route::get('event-list-by-id/{id}', 'data_by_id');
    });
    Route::controller(NoticeController::class)->group(function(){
        Route::post('notice-list','notice_list');
        Route::post('notice-list-for-homepage','notice_list_for_homepage');
        Route::post('notice-list','data');
    });
    Route::controller(OpsSecurityController::class)->group(function(){
        Route::post('opssecurity-list','opssecurity_list');
        Route::post('opssecurity-list-aprpoved','opssecurity_list_approved');
     
      
    });

    Route::controller(SliderController::class)->group(function(){
        Route::post('slider-by-slug','slider_by_slug');
        Route::post('slider-list','cms_data');
        Route::get('slider-by-id/{id}','cms_data_by_id');
        Route::post('slider-store','store');
        Route::post('slider-update/{id}','update');
        Route::delete('slider-delete/{id}','delete');
      
    });
    Route::controller(SlideController::class)->group(function(){
        Route::post('slide-list','cms_data');
        Route::get('slide-by-id/{id}','cms_data_by_id');
        Route::post('slide-store','store');
        Route::post('slide-update/{id}','update');
        Route::delete('slide-delete/{id}','delete');
      

  
    Route::controller(AvsecTrainingCalendarController::class)->group(function () {
        Route::post('avsecTraining-list-approved', 'avsecTrainingCalendar_list_approved');
    });
  Route::controller(WorkingAirportsController::class)->group(function () {
        Route::post('airport-list', 'airport_list');
        Route::post('airport-list-approved', 'airport_list_approved');
        Route::get('airport-list-by-id/{id}', 'data_by_id');


    });

    Route::controller(AirlinesController::class)->group(function () {
        Route::post('airline-list', 'airline_list');
        Route::post('airline-list-approved', 'airline_list_approved');
        Route::get('airline-list-by-id/{id}', 'data_by_id');

    });

    Route::controller(EventController::class)->group(function(){
        // Route::post('event-list','event_list');
        Route::post('event-list-for-homepage','event_list_for_homepage');
        Route::post('event-list','data');
        Route::get('event-list-by-id/{id}','data_by_id');
        Route::post('event-store','store');
        Route::post('event-update/{id}','update');
        Route::delete('event-delete/{id}','delete');
      


    Route::controller(CateringCompanyController::class)->group(function () {
        Route::post('catering-list', 'catering_list');
        Route::post('catering-list-approved', 'catering_list_approved');
        Route::get('catering-list-by-id/{id}', 'data_by_id');
        
    });

    Route::controller(OpsSecurityController::class)->group(function () {
        Route::post('opssecurity-list', 'opssecurity_list');
        Route::post('opssecurity-list-approved', 'opssecurity_list_approved');
        Route::get('opssecurity-list-by-id/{id}', 'data_by_id');
  
    });

    Route::controller(OpsiSecurityController::class)->group(function () {
        Route::post('opsisecurity-list', 'opsisecurity_list');
        Route::post('opsisecurity-list-approved', 'opsisecurity_list_approved');
        Route::get('opsisecurity-list-by-id/{id}', 'data_by_id');
     
    });
    Route::controller(ContactController::class)->group(function () {
        Route::post('contact-list','data');
    });
    Route::controller(VacancyController::class)->group(function(){
        Route::post('vacancy-list','data');
    });
    Route::controller(DivisionController::class)->group(function(){
        Route::post('division-list','data');
    });
    Route::controller(RegionController::class)->group(function(){
        Route::post('region-list','data');
        Route::post('region','region_list');
    });
    Route::controller(TenderController::class)->group(function(){
        Route::post('tender-list','data');
        Route::get('tender-details/{id}','data_by_id');
    
    });
    Route::controller(CommonTitleController::class)->group(function () {
        Route::post('title-list','index');

    });
    Route::controller(CircularController::class)->group(function () {
        Route::post('circular-list', 'circular_list');
        Route::post('circular-list','data');
        Route::post('circular-list-for-homepage', 'circular_list_for_homepage');
       
    });
    Route::controller(WorkingAirportsController::class)->group(function(){
        Route::post('airport-list','airport_list');
        Route::post('airport-list-aprpoved','airport_list_approved');
    });
    Route::controller(CateringCompanyController::class)->group(function(){
        Route::post('catering-list','catering_list');
        Route::post('catering-list-aprpoved','catering_list_approved');
     });
     Route::controller(AirlinesController::class)->group(function(){
        Route::post('airline-list','airline_list');
        Route::post('airline-list-aprpoved','airline_list_approved');
      
      
    });
});

// Admin Routes
Route::middleware(['cors', 'throttle:60,1', 'auth:admin_api'])->group(function () {
    Route::controller(menus::class)->group(function () {
        Route::post('menulist', 'index');
        Route::get('menu-by-id/{id}', 'data_by_id');
        Route::post('menu-store', 'store');
        Route::post('menu-update/{id}', 'update');
        Route::delete('menu-delete/{id}', 'delete');
        Route::post('menu/importCSV', 'importCSV');
    });

    Route::controller(SlideController::class)->group(function () {
        Route::post('slide-store', 'store_slide_api');
    });
    Route::controller(CateringCompanyController::class)->group(function(){
        Route::get('catering-list-by-id/{id}','data_by_id');
        Route::any('catering-add','store');
        Route::post('catering-update/{id}','update');
        Route::delete('catering-delete/{id}','delete');
      
    });
    Route::controller(AirlinesController::class)->group(function(){
        Route::get('airport-list-by-id/{id}','data_by_id');
        Route::any('airport-add','store');
        Route::post('airport-update/{id}','update');
        Route::delete('airport-delete/{id}','delete');
      
    });
    Route::controller(ActandpoliciesController::class)->group(function () {
        Route::post('acts-and-policies-store', 'store');
        Route::post('acts-and-policies-update/{id}', 'update');
        Route::delete('acts-and-policies-delete/{id}', 'delete');
    });

    Route::controller(EventController::class)->group(function () {
        Route::post('event-store', 'store');
        Route::post('event-update/{id}', 'update');
        Route::delete('event-delete/{id}', 'delete');
    });
    Route::controller(OpsSecurityController::class)->group(function(){
        Route::get('opssecurity-list-by-id/{id}','data_by_id');
        Route::any('opssecurity-add','store');
        Route::post('opssecurity-update/{id}','update');
        Route::delete('opssecurity-delete/{id}','delete');
      
    });
    Route::controller(WorkingAirportsController::class)->group(function(){
       
        Route::get('airport-list-by-id/{id}','data_by_id');
        Route::any('airport-add','store');
        Route::post('airport-update/{id}','update');
        Route::delete('airport-delete/{id}','delete');
      
    });
    Route::controller(CircularController::class)->group(function () {
        Route::post('circular-store', 'store');
        Route::get('circular-list-by-id/{id}', 'data_by_id');
        Route::post('circular-update/{id}', 'update');
        Route::delete('circular-delete/{id}', 'delete');
    });

    Route::controller(WorkingAirportsController::class)->group(function () {
        
        Route::get('airport-list-by-id/{id}', 'data_by_id');
        Route::post('airport-add', 'store');
        Route::post('airport-update/{id}', 'update');
        Route::delete('airport-delete/{id}', 'delete');
    });

    Route::controller(AirlinesController::class)->group(function () {
      
        Route::get('airline-list-by-id/{id}', 'data_by_id');
        Route::post('airline-add', 'store');
        Route::post('airline-update/{id}', 'update');
        Route::delete('airline-delete/{id}', 'delete');
    });

    Route::controller(CateringCompanyController::class)->group(function () {
        Route::get('catering-list-by-id/{id}', 'data_by_id');
        Route::post('catering-add', 'store');
        Route::post('catering-update/{id}', 'update');
        Route::delete('catering-delete/{id}', 'delete');
    });

    Route::controller(OpsSecurityController::class)->group(function () {
        Route::get('opssecurity-list-by-id/{id}', 'data_by_id');
        Route::post('opssecurity-add', 'store');
        Route::post('opssecurity-update/{id}', 'update');
        Route::delete('opssecurity-delete/{id}', 'delete');
    });

    Route::controller(OpsiSecurityController::class)->group(function () {
        Route::get('opsisecurity-list-by-id/{id}', 'data_by_id');
        Route::post('opsisecurity-add', 'store');
        Route::post('opsisecurity-update/{id}', 'update');
        Route::delete('opsisecurity-delete/{id}', 'delete');
    });
    Route::controller(NoticeController::class)->group(function () {
        Route::post('notice-store', 'store');
        Route::get('notice-list-by-id/{id}','data_by_id');
        Route::post('notice-update/{id}', 'update');
        Route::delete('notice-delete/{id}', 'delete');
    });

    Route::controller(TenderController::class)->group(function(){
        Route::post('tender-list','data');
        Route::get('tender-details/{id}','data_by_id');
      
        Route::post('tender-store', 'store');

        Route::get('tender-list-by-id/{id}','data_by_id');
        Route::post('tender-update/{id}', 'update');
        Route::delete('tender-delete/{id}', 'delete');
    });

    Route::controller(FormController::class)->group(function () {
        Route::post('form-store', 'store');
        Route::post('form-update/{id}', 'update');
        Route::delete('form-delete/{id}', 'delete');
    });

    Route::controller(VacancyController::class)->group(function () {
        Route::post('vacancy-store', 'store');
        Route::post('vacancy-update/{id}', 'update');
        Route::delete('vacancy-delete/{id}', 'delete');
    });

    Route::controller(ContactController::class)->group(function () {
        Route::post('contact-store', 'store');
        Route::post('contact-update/{id}', 'update');
        Route::delete('contact-delete/{id}', 'delete');
    });

    Route::controller(DivisionController::class)->group(function () {
        Route::post('division-store', 'store');
        Route::post('division-update/{id}', 'update');
        Route::delete('division-delete/{id}', 'delete');
    });

    Route::controller(RegionController::class)->group(function () {
        Route::post('region-store', 'store');
        Route::get('region-list-by-id/{id}','data_by_id');
        Route::post('region-update/{id}','update');
        Route::delete('region-delete/{id}','delete');
    });

    Route::controller(CommonTitleController::class)->group(function () {
        Route::get('title-list-by-id/{id}','data_by_id');
        Route::post('title-store','store');
        Route::post('title-update/{id}','update');
        Route::delete('title-delete/{id}','delete');
    });

    Route::controller(PermittedProhibitedController::class)->group(function(){
        Route::post('permitted-prohibited-list','index');
        Route::get('permitted-prohibited-list-by-id/{id}','data_by_id');
        Route::post('permitted-prohibited-store','store');
        Route::post('permitted-prohibited-update/{id}','update');
        Route::delete('permitted-prohibited-delete/{id}','delete');
      
    });
});

});

