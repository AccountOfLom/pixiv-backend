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
    //站点
    $router->resource('site', 'SiteController');
    //域名
    $router->resource('domain', 'DomainController');
    //动漫
    $router->resource('anime', 'AnimeController');
    //动漫系列
    $router->resource('anime_group', 'AnimeGroupController');
    //精选
    $router->resource('paint', 'PaintController');
    //图片上传
    $router->any('image', 'FileController@image');
    //作品排行
    $router->resource('illust_ranking', 'IllustRankingController');
    //会员
    $router->resource('member', 'MemberController');
    //收藏
    $router->resource('bookmark', 'BookmarkController');

});


