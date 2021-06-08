<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

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

//Route::middleware('auth:api')->prefix('v1')->group(function (Router $router) {
//});

//清除缓存
Route::post('redis/flushall', function () {
    $r = new \Predis\Client();
    $r->flushall();
    return 'success';
});

Route::middleware('site')->prefix('v1')->group(function () {
    //首页标签推荐和排行榜
    Route::get('home/tag-and-ranking', 'v1\IllustrationController@tagsAndR18');
    //r18日排行
    Route::get('ranking/day-r18', 'v1\IllustrationController@r18Day');
    //插画列表
    Route::get('illustrations', 'v1\IllustrationController@list');
    //发送验证码
//    Route::post('sms/send', 'v1\SMSController@sendVerificationCode');
    //会员注册
    Route::post('member/register', 'v1\MemberController@register');
    //会员登录
    Route::post('member/login', 'v1\MemberController@login');
    //里番列表
    Route::get('animes', 'v1\AnimeController@list');
    //精选列表
    Route::get('paints', 'v1\PaintController@list');
    //作者信息
    Route::get('author', 'v1\AuthorController@details');
    //标签搜索
    Route::get('search/tag', 'v1\searchController@tag');
    //热门标签
    Route::get('search/hot-tag', 'v1\searchController@hotTag');


    //需要登录
    Route::middleware('token')->group(function (Router $router) {
        //收藏列表
        Route::get('bookmarks', 'v1\BookmarkController@list');
        //收藏
        Route::post('bookmark/add', 'v1\BookmarkController@add');
        //取消收藏
        Route::post('bookmark/del', 'v1\BookmarkController@del');
        //关注
        Route::post('author/follow', 'v1\AuthorController@follow');
        //取消关注
        Route::post('author/unfollow', 'v1\AuthorController@unFollow');
        //关注列表
        Route::get('author/follow', 'v1\AuthorController@followList');
    });

});