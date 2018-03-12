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

Route::get('/', function () {
    return redirect()->route('glogin');
});

Route::get('glogin',array('as'=>'glogin','uses'=>'UserController@googleLogin'));
Route::get('google-user',array('as'=>'user.glist','uses'=>'UserController@listGoogleUser'));

Route::get('live-stream',array('as'=>'live.stream','uses'=>'LiveStreamController@stream'));
Route::get('log-out',array('as'=>'live.logout','uses'=>'LiveStreamController@logout'));

Route::post('live-search',array('as'=>'live.search','uses'=>'LiveStreamController@search'));
Route::post('live-post',array('as'=>'live.post','uses'=>'LiveStreamController@post'));
Route::get('live-broadcasts',array('as'=>'live.broadcasts','uses'=>'LiveStreamController@broadcasts'));
