<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Cms\Division\GalleryController;
use App\Http\Controllers\Cms\Division\DocumentController;
use App\Http\Controllers\Frontend\HomeController as frontend;
use App\Http\Controllers\Cms\Division\Training\AwardController;
use App\Http\Controllers\Cms\Division\Training\CenterController;
use App\Http\Controllers\Cms\Division\Training\CourseController;
use App\Http\Controllers\Cms\Division\GalleryCategoriesController;
use App\Http\Controllers\Cms\Division\DocumentCategoriesController;
use App\Http\Controllers\Frontend\InnerpagesController as innerpages;
use App\Http\Controllers\Cms\Division\Training\CertificatesController;
use App\Http\Controllers\Cms\Division\Training\AwardCategoriesController;
use App\Http\Controllers\Cms\Common\CommonTitleController as common_title;
use App\Http\Controllers\Cms\Division\Training\CourseCategoriesController;
use App\Http\Controllers\Cms\Division\Training\certificatesCategoriesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [frontend::class, 'index']);
Route::any('pages/{slug}', [innerpages::class, 'index']);
Route::get('my-captcha', 'Admin\Auth\LoginController@myCaptcha')->name('myCaptcha');
Route::any('/change-language', [frontend::class, 'changeLanguage']);


Route::get('refresh_captcha', 'Admin\Auth\LoginController@refreshCaptcha')->name('refresh_captcha');
Route::post('session/ajaxCheck', ['uses' => 'Admin\SessionController@ajaxCheck', 'as' => 'session.ajax.check']);

Route::group(['middleware' => 'resetLastActive'], function () {
    Route::get('/admin', 'Admin\DashboardController@index')->name('admin.dashboard');
});
Auth::routes();

//Route::get('/', 'HomeController@redirectAdmin')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

/**
 * Admin routes
 */
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
    Route::resource('roles', 'Admin\RolesController', ['names' => 'admin.roles']);
    Route::resource('users', 'Admin\UsersController', ['names' => 'admin.users']);
    Route::resource('admins', 'Admin\AdminsController', ['names' => 'admin.admins']);
    Route::resource('menus', 'Admin\MenuController', ['names' => 'admin.menus']);
    Route::any('search', 'Admin\SearchContoller@index')->name('admin.search');
    Route::any('get_primarylink_menu','Admin\AjaxRequestController@get_primarylink_menu')->name('admin.get_primarylink_menu');
    Route::any('update_menu_orders','Admin\AjaxRequestController@update_menu_orders')->name('admin.update_menu_orders');
    Route::any('get_menu_details','Admin\AjaxRequestController@get_menu_details')->name('admin.get_menu_details');
    //Route::resource('division/training/course', '\Cms\Division\Training\CourseController')->name( 'admin.division.training.course');
    Route::resource('division/training/course', CourseController::class, ['names' => 'admin.division.training.course']);
    Route::resource('division/training/course_categories', CourseCategoriesController::class, ['names' => 'admin.division.training.course_categories']);
    Route::resource('division/training/award', AwardController::class, ['names' => 'admin.division.training.award']);
    Route::resource('division/training/award_categories', AwardCategoriesController::class, ['names' => 'admin.division.training.award_categories']);
    Route::resource('division/training/certificates', CertificatesController::class, ['names' => 'admin.division.training.certificates']);
    Route::resource('division/training/certificates_categories',CertificatesCategoriesController::class, ['names' => 'admin.division.training.certificates_categories']);
    Route::resource('division/training/center', CenterController::class, ['names' => 'admin.division.training.center']);
    Route::resource('division/document_categories', DocumentCategoriesController::class, ['names' => 'admin.division.document_categories']);
    Route::resource('division/document', DocumentController::class, ['names' => 'admin.division.document']);
    Route::resource('division/gallery_categories', GalleryCategoriesController::class, ['names' => 'admin.division.gallery_categories']);
    Route::resource('division/gallery', GalleryController::class, ['names' => 'admin.division.gallery']);
    Route::resource('common_title', common_title::class, ['names' => 'admin.common_title']);
    
    // Login Routes
    Route::get('/login', 'Admin\Auth\LoginController@showLoginForm')->name('admin.login');
    Route::post('/login/submit', 'Admin\Auth\LoginController@login')->name('admin.login.submit');

    // Logout Routes
    Route::post('/logout/submit', 'Admin\Auth\LoginController@logout')->name('admin.logout.submit');

    // Forget Password Routes
    //Route::get('/password/reset', 'Admin\Auth\ForgetPasswordController@showLinkRequestForm')->name('admin.password.request');
    //Route::post('/password/reset/submit', 'Admin\Auth\ForgetPasswordController@reset')->name('admin.password.update');
});
