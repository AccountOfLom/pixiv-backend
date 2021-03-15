<?php

namespace App\Admin\Repositories;

use App\Models\SystemConfig as Model;
use Dcat\Admin\Repositories\EloquentRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SystemConfig extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    const ENABLE = "enable";
    const DISABLE = "disable";
    //爬虫开关
    const REPTILE_SWITCH = "reptile_switch";
    //p站爬虫接口
    const PIXIV_API_URL = "pixiv_api_url";
    //作者信息采集频率（分钟/次）
    const P_INTERVAL_AUTHOR = "p_interval_author";
    //作者信息采集开关
    const P_AUTHOR_SWITCH = "p_author_switch";
    //S3配置
    const S3 = "s3_cfg";
    //作者的作品采集开关
    const AUTHOR_ILLUSTS_SWITCH = 'author_illusts_switch';
    //作者的作品采集频率（分钟/次）
    const P_INTERVAL_AUTHOR_ILLUSTS = "p_interval_author_illusts";
    //允许显示的净网级别
    const SHOW_SANITY_LEVEL = "show_sanity_level";
    //限制级作品显示开关
    const SHOW_X_RESTRICT = "show_x_restrict";
    //下载作品图片频率
    const INTERVAL_DOWNLOAD_ILLUSTS_IMAGE = 'interval_download_illusts_image';
    //下载作品图片开关
    const DOWNLOAD_ILLUSTS_IMAGE_SWITCH = 'download_illusts_image_switch';

    /**
     * 根据key获取配置
     * @param $key
     * @return mixed|null
     * @throws \Throwable
     */
    public static function getConfig($key) {
        try {
            $value = Cache::get($key);
            if (!$value) {
                $cfg = Model::where('key', $key)->first();
                throw_if(!$cfg, \Exception::class, "配置查询失败 key:" . $key);
                $value = $cfg->value;
                Cache::put($key, $value);
            }
        } catch (\Exception $e) {
            Log::error("获取系统配置失败:" . $e->getMessage());
            return null;
        }
        return $value;
    }

}
