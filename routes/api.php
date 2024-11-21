<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Cms\{
    FormController, EventController, SlideController, NoticeController,
    RegionController, SliderController, TenderController, ContactController,
    VacancyController, VisitorController, AirlinesController, CircularController,
    DivisionController, OpsSecurityController, OpsiSecurityController,
    MenuController as Menus, ActandpoliciesController, CateringCompanyController,
    WorkingAirportsController, QuarterlyReportOnlineiiFormsController,
    CommonController as Common, LanguageController as Lang,OrganizationStructureController,
    Common\CommonTitleController, PermittedProhibitedController,QuizResultController,
    AvsecTrainingCalendarController,AstiVariousEntityController, QuarterlyReportOnlineFormsController,SecurityQuizController,MainGalleryController
};
use App\Http\Controllers\Cms\Division\GalleryController;
use App\Http\Controllers\Cms\FeedbackController as FeedbackController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\AdminsController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\SecurityQuestionController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RankController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => "api");

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

Route::middleware(['cors', 'throttle:60,1','removePoweredBy'])->group(function () {
    Route::controller(FeedbackController::class)->group(function () {
        Route::any('submit_feedback', 'store');
    });

    Route::controller(QuarterlyReportOnlineFormsController::class)->group(function () {
        Route::post('quarterly-report-online', 'store');
    });

    Route::controller(SecurityQuizController::class)->group(function () {
        Route::post('quiz-list', 'quiz_list');
        Route::get('quiz-list-by-id/{id}', 'data_by_id');
    });

    Route::controller(QuarterlyReportOnlineiiFormsController::class)->group(function () {
        Route::post('quarterly-report2-online', 'store');
    });
    Route::controller(QuizResultController::class)->group(function () {
       Route::post('saveScores', 'saveScores');
     });
});

Route::middleware(['cors','removePoweredBy'])->group(function () {
    Route::get('/csrf-token', fn () => response()->json(['csrfToken' => csrf_token()]));

    Route::controller(Lang::class)->group(function () {
        Route::post('langlist', 'index');
    });

    Route::controller(Menus::class)->group(function () {
        Route::post('menulist', 'index');
        Route::post('menu-list', 'menu_list');
        Route::post('menu/lang_slugs_wise', 'lang_slugs_wise');
        Route::post('menu/lang_pid_wise', 'lang_pid_wise');
        Route::post('menu/importCSV', 'importCSV');
    });

    Route::controller(Common::class)->group(function () {
        Route::post('common_title', 'index');
    });
    Route::controller(OrganizationStructureController::class)->group(function () {
        Route::post('organization-list', 'organization_list');
        Route::get('organization-structure-list-by-id/{id}', 'data_by_id');
    });
    Route::controller(AstiVariousEntityController::class)->group(function () {
        Route::post('asti-list', 'asti_list_approved');
        Route::get('asti-list-by-id/{id}','data_by_id');
    });

    Route::controller(VisitorController::class)->group(function () {
        Route::post('/visitor/count', 'getVisitorCount');
        Route::post('/visitor/increment', 'incrementVisitorCount');
    });
    Route::controller(SliderController::class)->group(function () {
        Route::post('slider-by-slug', 'slider_by_slug');
    });

    Route::controller(ActandpoliciesController::class)->group(function () {
        Route::post('acts-and-policies-list', 'data');
        Route::get('acts-and-policies-list-by-id/{id}', 'data_by_id');
    });

    Route::controller(AvsecTrainingCalendarController::class)->group(function () {
        Route::post('avsecTraining-list-approved', 'avsecTrainingCalendar_list_approved');
        Route::get('avsec-training-list-by-id/{id}','data_by_id');
    });

    Route::controller(EventController::class)->group(function () {
        Route::post('event-list-frontend', 'event_list_for_frontend');
        Route::post('archive-event-list', 'archive');
        Route::get('event-list-by-id/{id}', 'data_by_id');
        Route::post('event-list-for-homepage', 'event_list_for_homepage');
    });

    Route::controller(NoticeController::class)->group(function () {
        Route::post('notice-list', 'notice_list');
        Route::post('archive-notice-list', 'archive');
        Route::post('notice-list-for-homepage', 'notice_list_for_homepage');
        Route::get('notice-list-by-id/{id}', 'data_by_id');
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

    Route::controller(CircularController::class)->group(function () {
        Route::post('circular-list', 'data');
        Route::post('archive-circular-list', 'archive');
        Route::post('circular-list-for-homepage', 'circular_list_for_homepage');
    });

    Route::controller(ContactController::class)->group(function () {
        Route::post('contact-list', 'index');
    });

    Route::controller(VacancyController::class)->group(function () {
        Route::post('vacancy-list', 'data');
        Route::post('vacancy-archive-list', 'archive');
    });

    Route::controller(DivisionController::class)->group(function () {
        Route::post('division-list', 'data');
    });

    Route::controller(RegionController::class)->group(function () {
        Route::post('region-list', 'region_list');
        Route::post('region', 'region_list');
    });

    Route::controller(TenderController::class)->group(function () {
        Route::post('tender-list', 'data');
        Route::post('tender-archive-list', 'archive');
        Route::get('tender-details/{id}', 'data_by_id');
    });

    Route::controller(CommonTitleController::class)->group(function () {
        Route::post('title-list', 'index');
    });

    Route::controller(PermittedProhibitedController::class)->group(function () {
        Route::post('permitted-prohibited-list', 'index');
        Route::get('permitted-prohibited-list-by-id/{id}', 'data_by_id');
    });

    Route::controller(WorkingAirportsController::class)->group(function () {
        Route::post('airport-list', 'airport_list');
        Route::get('airport-list-by-id/{id}','data_by_id');
        Route::post('airport-list-approved', 'airport_list_approved');
    });

    Route::controller(CateringCompanyController::class)->group(function () {
        Route::post('catering-list', 'catering_list');
        Route::get('catering-list-by-id/{id}', 'data_by_id');
        Route::post('catering-list-approved', 'catering_list_approved');
    });

    Route::controller(AirlinesController::class)->group(function () {
        Route::post('airline-list', 'airline_list');
        Route::post('airline-list-approved', 'airline_list_approved');
    });
    Route::controller(SecurityQuestionController::class)->group(function () {
        Route::get('question-list', 'questions');
        Route::post('re-register', 'reRegister');
        Route::post('forgot-password', 'forgotPassword');
        Route::post('otp-verification', 'verifyOtp');
        Route::post('quiz-results', 'store');
        
    });
    Route::controller(MainGalleryController::class)->group(function(){
        Route::post('gallery-list','data');
        Route::get('gallery-list-by-id/{id}','cms_data_by_id');
    });
    
    // Route::controller(RolesController::class)->group(function () {
    //     Route::post('roles-list', 'index');
    // });
   
});

// Admin Routes
Route::middleware(['cors', 'throttle:30,1', 'auth:admin_api','removePoweredBy'])->group(function () {
    Route::controller(RolesController::class)->group(function () {
        Route::post('roles-list', 'cms_data');
        Route::post('roles-store', 'store');
        Route::post('roles-update/{id}','update');
        Route::delete('roles-delete/{id}','destroy');
        Route::post('all_permissions', 'all_permissions');
        Route::get('roles-list-by-id/{id}', 'edit');
        Route::get('roles', 'role_list');
    });
    Route::controller(AdminsController::class)->group(function () {
        Route::post('adminList', 'index');
        Route::post('admin-list', 'cms_data');
        Route::get('admin-list-by-id/{id}', 'data_by_id');
        Route::put('users/{user}/status', 'updateStatus');
        Route::post('admin-store', 'Cms_store');
        Route::post('admin-update/{id}', 'Cms_update');
        Route::delete('admin-delete/{id}', 'delete');
      
    });
    Route::controller(menus::class)->group(function () {
        Route::post('menus-list', 'cms_data');
        Route::post('menu-child-list', 'cms_menu_list');
        Route::post('menus/lang_pid_wise', 'cms_lang_pid_wise');
        Route::get('menu-by-id/{id}', 'data_by_id');
        Route::post('menu-store', 'store');
        Route::post('menu-update/{id}', 'update');
        Route::delete('menu-delete/{id}', 'delete');

        Route::post('menu/importCSV', 'importCSV');
    });
    Route::controller(SliderController::class)->group(function(){
      //  Route::post('slider-by-slug','slider_by_slug');
        Route::get('slider-dropdown','slider_dropdown');
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
      

    });

    Route::controller(ActandpoliciesController::class)->group(function () {
        Route::post('acts-and-policies','cms_data');
        Route::get('acts-and-policies-list-by-id/{id}','data_by_id');
        Route::post('acts-and-policies-store', 'store');
        Route::post('acts-and-policies-update/{id}', 'update');
        Route::delete('acts-and-policies-delete/{id}', 'delete');
    });

    Route::controller(EventController::class)->group(function () {
        Route::post('events-list','cms_data');
        Route::post('event-store', 'store');
        Route::post('event-update/{id}', 'update');
        Route::delete('event-delete/{id}', 'delete');
    });
    Route::controller(OpsSecurityController::class)->group(function(){
        Route::post('opssecurities-list', 'data');
        Route::any('opssecurity-store','store');
        Route::post('opssecurity-update/{id}','update');
        Route::delete('opssecurity-delete/{id}','delete');
      
    });
    Route::controller(WorkingAirportsController::class)->group(function(){
        Route::post('airports-list', 'cms_data');
        Route::any('airport-store','cms_store');
        Route::post('airport-update/{id}','cms_update');
        Route::delete('airport-delete/{id}','cms_delete');
      
    });
    Route::controller(CircularController::class)->group(function () {
        Route::post('circulars-list','cms_data');
        Route::post('circular-store', 'store');
        Route::get('circular-list-by-id/{id}', 'data_by_id');
        Route::post('circular-update/{id}', 'update');
        Route::delete('circular-delete/{id}', 'delete');
    });

    Route::controller(AirlinesController::class)->group(function () {
        Route::post('all-airline-list', 'cms_data');
        Route::get('airline-list-by-id/{id}', 'data_by_id');
        Route::post('airline-store', 'cms_store');
        Route::post('airline-update/{id}', 'cms_update');
        Route::delete('airline-delete/{id}', 'cms_delete');
    });

    Route::controller(CateringCompanyController::class)->group(function () {
        Route::post('caterings-list', 'cms_data');
        Route::post('catering-store', 'cms_store');
        Route::post('catering-update/{id}', 'cms_update');
        Route::delete('catering-delete/{id}', 'cms_delete');
    });

    Route::controller(OpsiSecurityController::class)->group(function () {
        Route::post('opsisecurities-list', 'cms_data');
        Route::post('opsisecurity-store', 'cms_store');
        Route::post('opsisecurity-update/{id}', 'cms_update');
        Route::delete('opsisecurity-delete/{id}', 'cms_delete');
    });
    Route::controller(NoticeController::class)->group(function () {
        Route::post('notices-list','cms_data');
        Route::post('notice-store', 'store');
        Route::get('notice-list-by-id/{id}','data_by_id');
        Route::post('notice-update/{id}', 'update');
        Route::delete('notice-delete/{id}', 'delete');
    });

    Route::controller(TenderController::class)->group(function(){
       Route::post('tenders-list','cms_data');
        Route::post('tender-store', 'store');
        Route::get('tender-list-by-id/{id}','data_by_id');
        Route::post('tender-update/{id}', 'update');
        Route::delete('tender-delete/{id}', 'delete');
    });

    Route::controller(FormController::class)->group(function () {
        Route::post('forms-list','cms_data');
        Route::get('form-list-by-id/{id}','data_by_id');
        Route::post('form-store', 'store');
        Route::post('form-update/{id}', 'update');
        Route::delete('form-delete/{id}', 'delete');
    });

    Route::controller(VacancyController::class)->group(function () {
        Route::post('vacancys-list', 'cms_data');
        Route::get('vacancy-list-by-id/{id}','data_by_id');
        Route::post('vacancy-store', 'store');
        Route::post('vacancy-update/{id}', 'update');
        Route::delete('vacancy-delete/{id}', 'delete');
    });

    Route::controller(ContactController::class)->group(function () {
       Route::post('contacts-list','data');
       Route::get('contact-list-by-id/{id}','data_by_id');
        Route::post('contact-store', 'store');
        Route::post('contact-update/{id}', 'update');
        Route::delete('contact-delete/{id}', 'delete');
    });

    Route::controller(DivisionController::class)->group(function () {
        Route::post('division-list','data');
        Route::post('division-list-api','data');
        Route::post('division-store', 'store');
        Route::get('division-list-by-id/{id}','data_by_id');
        Route::post('division-update/{id}', 'update');
        Route::delete('division-delete/{id}', 'delete');
        Route::get('division-dropdown-list/{lang_code}','division_dropdown_list');
    });

    Route::controller(RegionController::class)->group(function () {
        Route::post('region-list','data');
       // Route::post('region','region_list');
        Route::post('region-store', 'store');
        Route::get('region-list-by-id/{id}','data_by_id');
        Route::post('region-update/{id}','update');
        Route::delete('region-delete/{id}','delete');
        Route::get('region-dropdown-list/{lang_code}','region_dropdown_list');
    });

    Route::controller(CommonTitleController::class)->group(function () {
        Route::post('title-list','index');
        Route::get('title-list-by-id/{id}','data_by_id');
        Route::post('title-store','store');
        Route::post('title-update/{id}','update');
        Route::delete('title-delete/{id}','delete');
    });

    Route::controller(PermittedProhibitedController::class)->group(function(){
        //Route::post('permitted-prohibited-list','index');
        Route::get('permitted-prohibited-list-by-id/{id}','data_by_id');
        Route::post('permitted-prohibited-store','store');
        Route::post('permitted-prohibited-update/{id}','update');
        Route::delete('permitted-prohibited-delete/{id}','delete');
      
    });
    Route::controller(SliderController::class)->group(function(){
        //Route::post('slider-by-slug','slider_by_slug');
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
      
    });
    Route::controller(GalleryController::class)->group(function(){
        Route::post('division-gallery-list','data');
        Route::post('division-gallerys-list','cms_data');
        Route::get('division-gallery-by-id/{id}','cms_data_by_id');
        Route::post('division-gallery-store','store');
        Route::post('division-gallery-update/{id}','update');
        Route::delete('division-gallery-delete/{id}','delete');
      
    });
    Route::controller(MainGalleryController::class)->group(function(){
        Route::post('gallerys-list','cms_data');
        Route::post('gallery-store','store');
        Route::post('gallery-update/{id}','update');
        Route::delete('gallery-delete/{id}','delete');
      
    });
    Route::controller(DocumentController::class)->group(function(){
        Route::post('admin-document-list','data');
        Route::get('admin-document-by-id/{id}','data_by_id');
        Route::post('admin-document-store','store');
        Route::post('admin-document-update/{id}','update');
        Route::delete('admin-document-delete/{id}','delete');
        Route::post('admin-document','show_Document');
    });
    Route::controller(DocumentCategoryController::class)->group(function(){
        Route::post('admin-document-category-list','data');
        Route::get('admin-document-category-by-id/{id}','data_by_id');
        Route::post('admin-document-category-store','store');
        Route::post('admin-document-category-update/{id}','update');
        Route::delete('admin-document-category-delete/{id}','delete');
        Route::get('admin-document-category','document_categories');
    });
    Route::controller(AuditController::class)->group(function () {
        Route::post('audit-list', 'index');
        Route::post('audit-report-download', 'exportPDF');
    });
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard-data', 'cms_data');
        Route::get('dashboard-count-data', 'cms_count_data');
    });
    Route::controller(RankController::class)->group(function () {
        Route::get('rank-dropdown-list', 'dropdown_list');
    });
    Route::controller(AstiVariousEntityController::class)->group(function () {
        Route::post('astis-list', 'cms_data');
        Route::any('asti-store','cms_store');
        Route::post('asti-update/{id}','cms_update');
        Route::delete('asti-delete/{id}','cms_delete');
    });
    Route::controller(AvsecTrainingCalendarController::class)->group(function () {
        Route::post('avsec-trainings-list', 'cms_data');
        Route::any('avsec-training-store','cms_store');
        Route::post('avsec-training-update/{id}','cms_update');
        Route::delete('avsec-training-delete/{id}','cms_delete');
    });
    Route::controller(SecurityQuizController::class)->group(function () {
        Route::post('quizs-list', 'cms_data');
        Route::any('quiz-store','cms_store');
        Route::post('quiz-update/{id}','cms_update');
        Route::delete('quiz-delete/{id}','cms_delete');
    });
    Route::controller(OrganizationStructureController::class)->group(function () {
        Route::post('organization-structure-list', 'cms_data');
        Route::any('organization-structure-store','cms_store');
        Route::post('organization-structure-update/{id}','cms_update');
        Route::delete('organization-structure-delete/{id}','cms_delete');
    });
    Route::controller(QuizResultController::class)->group(function () {
        Route::post('quiz-results-list', 'cms_data');
        Route::get('quiz-result-list-by-id/{id}', 'data_by_id');
    });
    Route::controller(QuarterlyReportOnlineFormsController::class)->group(function () {
        Route::post('quarterly-report-online-list', 'cms_data');
    });
    Route::controller(QuarterlyReportOnlineiiFormsController::class)->group(function () {
        Route::post('quarterly-report-onlineii-list', 'cms_data');
    });
});

