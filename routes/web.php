<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

Route::get('/test', function () {
    (new \App\Console\Commends\Author())->handle();

//    $filePath = 'E:\phpstudy_pro\WWW\20210312194840.png';
//    $img = \Intervention\Image\Facades\Image::make($filePath);
//    if (!$img) {
//        Log::error("图片文件不存在，filePath:" . $filePath);
//        return false;
//    }
//    return (new \App\Server\Bucket\S3())->deletedObject($img->basename);
//    return (new \App\Server\Bucket\S3())->putObject($img->basename, $img->mime(), $filePath);
});
