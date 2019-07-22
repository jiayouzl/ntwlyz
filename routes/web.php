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

Route ::get('/', function () {
	return view('welcome');
});

//Route::get('/test',function(){
//	return \Carbon\Carbon::parse('2019-07-14 02:08:37')->addDays(365)->toDateTimeString();
//});

/*
 * 注册与验证通用接口
 * Url:http://127.0.0.1:8000/login?key=12345
 * */
Route ::get('/login', 'KeyLoginController@login') -> middleware('CheckDebug');

/*
 * 充值接口
 * Url:http://127.0.0.1:8000/pay?key=12345&card=23115434543&password=123123123
 * */
Route::get('/pay','KeyLoginController@pay');

