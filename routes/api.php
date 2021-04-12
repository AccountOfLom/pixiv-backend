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

Route::prefix('v1')->group(function (Router $router) {
    //r18日排行
    $router->get('ranking-day-r18', 'v1\IllustRankingController@r18Day');
});