<?php

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Dcat\Admin\Admin;

Admin::routes();

Route::group([
    'prefix'     => config('admin.route.prefix'),
    'namespace'  => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //系统设置
    $router->resource('system_config', 'SystemConfigController');

    //作者
    $router->resource('author', 'AuthorController');

    //插画
    $router->resource('illustration', 'IllustrationController');

    //标签
    $router->resource('tag', 'TagController');

});


