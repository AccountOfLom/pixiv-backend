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
    const P_SWITCH_AUTHOR = "p_switch_author";

    //S3配置
    const S3 = "s3_cfg";

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
