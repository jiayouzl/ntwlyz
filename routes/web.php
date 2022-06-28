<?php
use Illuminate\Routing\Router;

Route::get('/','KeyLoginController@index');

Route ::group([
    'prefix'     => 'rules',
    'middleware' => 'CheckDebug'
], function (Router $route) {
    //注册与验证通用接口
    //Url:http://127.0.0.1:8000/rules/login?key=12345 (已启用签名,Url必须要签名后才能访问)
    $route->get('login','KeyLoginController@login');
    //充值接口
    //Url:http://127.0.0.1:8000/rules/pay?key=12345&card=23115434543&password=123123123
    $route->get('pay','KeyLoginController@pay');
    //授权转移
    //Url:http://127.0.0.1:8000/rules/replace?key1=12345&key2=11111
    $route->get('replace','KeyLoginController@replace');
    //URL签名
    //Url:http://127.0.0.1:8000/rules/urlsign?key=12345
    $route->get('urlsign','KeyLoginController@urlsign');
});
