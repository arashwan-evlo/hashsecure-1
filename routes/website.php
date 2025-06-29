<?php

/*
|--------------------------------------------------------------------------
| Web Routes for website
|--------------------------------------------------------------------------
*/
Route::get('web/{slug}','WebsiteController@index');

/*Tem work*/
Route::get('/form-input','TempworkController@temp_input');
Route::Post('/form-input','TempworkController@post_temp_input');