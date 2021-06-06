<?php


namespace App\Cache;


use App\Admin\Repositories\Illustration;
use App\Admin\Repositories\SystemConfig;
use App\Admin\Repositories\Tag;
use App\Models\Author;
use App\Models\IllustImage;
use Predis\Client;

class IllustCache
{
    private static $cacheKey = 'illust_full_';

    private static $expire = 3600;

    /**
     * @param $pixivID
     * @param $update 更新缓存操作获取数据
     * @return array|mixed
     * @throws \Throwable
     */
    public static function get($pixivID, $update = false)
    {
        $cacheKey = self::$cacheKey . $pixivID;
        $r = (new Client());
        if ($r->exists($cacheKey)) {
            $result = json_decode($r->get($cacheKey), true);
            if ($update) {
                return $result;
            }

            if ($result['author_collected'] == 1) {
                $result['author']['profile_image_url'] = SystemConfig::getS3ResourcesURL($result['author']['profile_image_url']);
                $result['author']['background_image_url'] = SystemConfig::getS3ResourcesURL($result['author']['background_image_url']);
            }

            $result['images'] = SystemConfig::getS3ResourcesURL($result['images']);
            return $result;
        }

        $result = [];
        $illust = \App\Models\Illustration::where('pixiv_id', $pixivID)->first();
        if (!$illust) {
            return [];
        }

        //基本信息
        $result['id'] = $illust->id;
        $result['pixiv_id'] = $illust->pixiv_id;
        $result['title'] = $illust->title;
        $result['caption'] = $illust->caption;
        $result['x_restrict'] = $illust->x_restrict;
        $result['width'] = $illust->width;
        $result['height'] = $illust->height;
        $result['page_count'] = $illust->page_count;
        $result['total_view'] = $illust->total_view;
        $result['total_bookmarks'] = $illust->total_bookmarks;
        $result['type'] = $illust->type;
        $result['type_text'] = (new Illustration())->typeText[$illust->type];
        $result['author_collected'] = $illust->author_collected;

        //作者信息
        $result['author'] = [
            'pixiv_id' => $illust->author_pixiv_id,
            'collected' => $illust->author_collected
        ];

        if ($illust->author_collected == 1) {
            $author = Author::where('pixiv_id', $illust->author_pixiv_id)->first();
            $result['author']['id'] = $author->id;
            $result['author']['name'] = $author->name;
            $result['author']['profile_image_url'] = $author->profile_image_url;
        }

        //标签
        $tags = [];
        $tagIDs = explode(',', $illust->tag_ids);
        foreach ($tagIDs as $tagID) {
            $tags[] = (new Tag())->getTagByID($tagID);
        }
        $result['tags'] = $tags;

        //图片
        $result['images'] = [];
        if ($illust->image_collected) {
            $images = IllustImage::where(['illust_id' => $pixivID, 'is_collected' => 1])->get();
            if ($images) {
                foreach ($images as $image) {
                    $result['images'][] = [
                        'square_medium_url' => $image['square_medium_url'],
                        'medium_url' => $image['medium_url'],
                        'original_url' => $image['original_url'],
                    ];
                }
            }
        }

        if ($update) {
            return $result;
        }

        $r->set($cacheKey, json_encode($result), 'ex', self::$expire);

        if ($result['author_collected'] == 1) {
            $result['author']['profile_image_url'] = SystemConfig::getS3ResourcesURL($result['author']['profile_image_url']);
        }
        $result['images'] = SystemConfig::getS3ResourcesURL($result['images']);

        return $result;
    }

    /**
     * 删除缓存
     * @param $pixivID
     * @return bool
     */
    public static function del($pixivID)
    {
        $cacheKey = self::$cacheKey . $pixivID;
        $r = (new Client());
        if ($r->exists($cacheKey)) {
            $r->del($cacheKey);
        }
        return true;
    }


    public static function IDMapping($id)
    {
        $cacheKey = 'illust_id_' . $id;
        $r = (new Client());
        if ($r->exists($cacheKey)) {
            return $r->get($cacheKey);
        }
        $illust = \App\Models\Illustration::find($id);
        if (!$illust) {
            return null;
        }
        $r->set($cacheKey, $illust->pixiv_id, 'ex', 3600);
        return $illust->pixiv_id;
    }

    public static function update($pixivID, $key, $value)
    {
        $cacheData = self::get($pixivID, true);
        $cacheData[$key] = $value;
        $cacheKey = self::$cacheKey . $pixivID;
        $r = (new Client());
        if ($r->exists($cacheKey)) {
            $r->del($cacheKey);
        }
        $r->set($cacheKey, json_encode($cacheData), 'ex', self::$expire);
    }
}