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

/*
 * 注册与验证通用接口
 * Url:http://127.0.0.1:8000/login?key=12345(已启用签名,Url必须要签名后才能访问)
 * */
Route ::get('/login', 'KeyLoginController@login') -> middleware(['CheckDebug']);

/*
 * 充值接口
 * Url:http://127.0.0.1:8000/pay?key=12345&card=23115434543&password=123123123
 * */
Route ::get('/pay', 'KeyLoginController@pay');

/*
 * URL签名
 * Url:http://127.0.0.1:8000/urlsign?key=12345
 */
Route ::get('/urlsign', 'KeyLoginController@urlsign');

//http://127.0.0.1:8000/urlsign/12345
//Route ::get('urlsign/{key}', function ($key) {
//    return UrlSigner ::sign(env('APP_URL') . '/login?key=' . $key, Carbon ::now() -> addMinutes(120));
//});
