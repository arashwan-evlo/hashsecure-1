<?php

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
Route::middleware(['web',  'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu'])
   ->prefix('sitemanager')->group(function() {
        Route::get('/install', 'InstallController@index');
        Route::post('/install', 'InstallController@install');
        Route::get('/install/uninstall', 'InstallController@uninstall');
        Route::get('/install/update', 'InstallController@update');

        //Products
        Route::get('/products/', 'SitemanagerController@index');


        //Media
        Route::get('/media/', 'SitemanagerController@media');
        Route::get('/media/edit/{id?}', 'SitemanagerController@media_edit');
        Route::post('/media/sotre/', 'SitemanagerController@media_sotre');
        Route::post('/media/delete/{id}', 'SitemanagerController@media_delete');


        Route::get('/shipping_governorate', 'SitemanagerController@shipping_governorate');
        Route::get('/shipping_governorate/edit/{id}', 'SitemanagerController@governorate_edit');
        Route::post('/shipping_governorate/store', 'SitemanagerController@governorate_store');
        Route::post('/shipping_governorate/delete/{id}', 'SitemanagerController@governorate_delete');


    });
