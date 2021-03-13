<?php

namespace App\Console;

use App\Admin\Repositories\SystemConfig;
use App\Server\Bucket\S3;
use App\Server\Pixiv;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

trait Common
{
    /**
     * 频率限制
     * @param $intervalKey
     * @param $intervalTimeKey
     * @return bool
     * @throws \Throwable
     */
    protected function intervalAllow($intervalKey, $intervalTimeKey) {
        //采集频率（分钟）
        $interval = SystemConfig::getConfig($intervalKey);
        if ($interval === null) {
            Log::error("采集频率获取失败,key:" . $intervalKey);
            return false;
        }

        $preTime = Cache::get($intervalTimeKey);
        if ($preTime && $preTime > time() - $interval * 60) {
            return false;   //未到下一次采集时间
        }

        $cacheRes = Cache::put($intervalTimeKey, time());
        if (!$cacheRes) {
            Log::error("采集时间缓存失败，key:" . $intervalTimeKey);
            return false;
        }

        return true;
    }

    /**
     * 图片下载、上传、删除
     * @param $pixivURL
     * @return array|bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function imgDownloadAndUploadAndDel($pixivURL) {
        //下载头像
        $pixiv = new Pixiv();

        $baseName = $pixiv->getImageBaseName($pixivURL);

        $download = $pixiv->imageDownload($pixivURL, $pixiv->imageDownloadPath);
        if (!$download) {
            Log::error("文件下载失败,url：" . $pixivURL);
            return false;
        }

        $fullPath = $pixiv->imageDownloadPath . '/' . $baseName;
        if (!file_exists($fullPath)) {
            Log::error("文件未找到,file path：" . $fullPath);
            return false;
        }

        //上传到云存储
        $S3 = new S3();
        $img = Image::make($fullPath);
        $objectURL = $S3->putObject($baseName, $img->mime(), $fullPath);
        if (!$objectURL) {
            Log::error("文件上传至S3 失败, imgFullPath：" . $fullPath);
            return false;
        }

        if (!unlink($fullPath)) {
            Log::error("文件删除失败,file path：" . $fullPath);
            return false;
        }

        return [
            'width'     => $img->getWidth(),
            'height'    => $img->getHeight(),
            'url'       => $objectURL
        ];
    }
}
