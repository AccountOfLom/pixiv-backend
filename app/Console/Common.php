<?php

namespace App\Console;

use App\Admin\Repositories\SystemConfig;
use App\Models\IllustImage;
use App\Models\Illustration;
use App\Models\Tag;
use App\Server\Bucket\S3;
use App\Server\Pixiv;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Predis\Client;

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
            echo 'a';
            return false;
        }

        $preTime = Cache::get($intervalTimeKey);
        if ($preTime && $preTime > time() - $interval * 60) {
            echo 'b';
            return false;   //未到下一次采集时间
        }

        $cacheRes = Cache::put($intervalTimeKey, time());
        if (!$cacheRes) {
            echo 'c';
            Log::error("采集时间缓存失败，key:" . $intervalTimeKey);
            return false;
        }

        return true;
    }

    /**
     * 保存作品信息
     * @param $data
     * @param int $authorCollected
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function saveIllusts($data, $authorCollected = 0) {

        $illustsExist = Illustration::where('pixiv_id', $data['id'])->first();
        if ($illustsExist) {
            return true;
        }

        $illusts = new Illustration();

        $illusts->pixiv_id = $data['id'];
        $illusts->author_pixiv_id = $data['user']['id'];
        $illusts->author_collected = $authorCollected;
        $illusts->title = $data['title'];
        $illusts->type = $data['type'];
        $illusts->caption = $data['caption'];
        $illusts->x_restrict = $data['x_restrict'];
        $illusts->sanity_level = $data['sanity_level'];
        $illusts->width = $data['width'];
        $illusts->height = $data['height'];
        $illusts->page_count = $data['page_count'];
        $illusts->image_collected = 0;
        $illusts->height = $data['height'];
        $illusts->create_date = date('Ymd', strtotime($data['create_date']));
        $illusts->total_view = $data['total_view'];
        $illusts->total_bookmarks = $data['total_bookmarks'];

        if ($data['tags'] && count($data['tags'])) {
            $illusts->tag_ids = implode(',', $this->getTagIDs($data['tags']));
        }

        $illustImages = [];
        if ($data['page_count'] == 1) {

            $illustImage = [
                'illust_id' => $data['id'],
                'p_square_medium_url' => $data['image_urls']['square_medium'],
                'p_medium_url' => $data['image_urls']['medium'],
                'p_large_url' => $data['image_urls']['large'],
                'p_original_url' => $data['meta_single_page']['original_image_url'],
                'is_collected' => 0
            ];
            //动画
            if ($data['type'] == \App\Admin\Repositories\Illustration::TYPE_UGOIRA) {
                $ugoira = $this->getUgoiraMetadata($data['id']);
                $illustImage['ugoira_zip_url'] = $ugoira['ugoira_metadata']['zip_urls']['medium'];
                $illustImage['ugoira_frame'] = json_encode($ugoira['ugoira_metadata']['frames']);
            }
            $illustImages[] = $illustImage;

        } else {

            foreach ($data['meta_pages'] as $k => $v) {
                $illustImages[] = [
                    'illust_id' => $data['id'],
                    'p_square_medium_url' => $v['image_urls']['square_medium'],
                    'p_medium_url' => $v['image_urls']['medium'],
                    'p_large_url' => $v['image_urls']['large'],
                    'p_original_url' => $v['image_urls']['original'],
                    'is_collected' => 0
                ];
            }

        }

        $this->saveillustImages($illustImages);

        return $illusts->save();
    }

    /**
     * 获取动画信息
     * @param $illustID
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function getUgoiraMetadata($illustID) {
        $pixiv = new Pixiv();
        $data = $pixiv->ugoiraMetadata($illustID);
        return $data;
    }

    /**
     * 返回作品标签IDs
     * @param $illustsTags
     * @return array
     */
    protected function getTagIDs($illustsTags) {
        $tagIDs = [];

        foreach ($illustsTags as $k => $illustsTag) {

            $tagCache = (new \App\Admin\Repositories\Tag())->getTagByName($illustsTag['name']);
            if ($tagCache) {
                if (!is_object($tagCache)) {
                    dd($tagCache);
                }
                $tagIDs[] = $tagCache->id;
                continue;
            }

            $tag = new Tag();
            $tag->name = $illustsTag['name'];
            $tag->translated_name = $illustsTag['translated_name'];
            $tag->is_collected = 0;
            $tag->save();

            $tagIDs[] = $tag->id;
        }

        return $tagIDs;
    }

    /**
     * 保存作品
     * @param $images
     */
    protected function saveillustImages($images) {
        foreach ($images as $k => $v) {
            $image = new IllustImage();
            $image->illust_id = $v['illust_id'];
            $image->p_square_medium_url = $v['p_square_medium_url'];
            $image->p_medium_url = $v['p_medium_url'];
            $image->p_large_url = $v['p_large_url'];
            $image->p_original_url = $v['p_original_url'] ?? '';
            $image->is_collected = $v['is_collected'];
            $image->p_ugoira_zip_url = $v['ugoira_zip_url'] ?? '';
            $image->ugoira_frame = $v['ugoira_frame'] ?? '';
            $image->save();
        }
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

        $baseName = $pixiv->getFileBaseName($pixivURL);

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

        return $objectURL;

//        return [
//            'width'     => $img->getWidth(),
//            'height'    => $img->getHeight(),
//            'url'       => $objectURL
//        ];
    }

    /**
     * zip下载、上传、删除
     * @param $pixivURL
     * @return array|bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    protected function zipDownloadAndUploadAndDel($pixivURL) {
        //下载zip
        $pixiv = new Pixiv();

        $baseName = $pixiv->getFileBaseName($pixivURL);

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
        $objectURL = $S3->putObject($baseName, 'x-zip-compressed', $fullPath);
        if (!$objectURL) {
            Log::error("文件上传至S3 失败, imgFullPath：" . $fullPath);
            return false;
        }

        if (!unlink($fullPath)) {
            Log::error("文件删除失败,file path：" . $fullPath);
            return false;
        }
        return $fullPath;
    }
}
