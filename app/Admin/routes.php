<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index')->name('admin.home');
	$router->resource('users', UsersController::class);
	$router->resource('cards', CardController::class);
	$router->get('setting', 'SettingController@setting');
	$router->get('cardplsc', 'CardzhizuoController@index');
	$router->get('buchang','YongHuBuChangController@index');
});
