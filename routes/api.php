<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Response;
use App\Http\Controllers\Api\Frontend\MenuController as menus;
use App\Http\Controllers\Api\Frontend\LanguageController as lang;
use App\Http\Controllers\Api\Frontend\CommonController as Common;
use App\Http\Controllers\Api\Frontend\VisitorController;
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
    Route::post('slider-by-slug', 'CMS\SliderController@slider_by_slug')->name('cms.slider.by.slug');
});