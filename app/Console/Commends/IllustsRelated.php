<?php
/**
 * Created by PhpStorm.
 * User: shali
 * Date: 2021/4/3
 * Time: 10:14
 */

namespace App\Console\Commends;

use App\Admin\Repositories\SystemConfig;
use App\Console\Common;
use App\Models\IllustImage;
use App\Models\Illustration;
use App\Server\Pixiv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * 相关作品
 * Class IllustsRelated
 * @package App\Console\Commends
 */
class IllustsRelated extends Command
{
    use Common;

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'illusts-related';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'illusts-related';

    protected $pixiv;


    function __construct()
    {
        parent::__construct();
        $this->pixiv = new Pixiv();
    }


    /**
     * 执行控制台命令
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function handle()
    {
        //采集开关
        $switch = SystemConfig::getConfig(SystemConfig::ILLUSTS_RELATED_SWITCH);
        if (!$switch || $switch != SystemConfig::ENABLE) {
            return false;
        }

        //采集频率（分钟）
        $preTimeKey = "collection_illusts_related_time";
        $allow = $this->intervalAllow(SystemConfig::ILLUSTS_RELATED_INTERVAL, $preTimeKey);
        if (!$allow) {
            return false;
        }

        $illust = Illustration::where(['get_related' => 1, 'related_collected' => 0])->first();
        if (!$illust) {
            return false;
        }

        //作品列表
        set_time_limit(0);
        $saveRes = $this->illustRelatedAll($illust->pixiv_id);
        $illust->related_collected = $saveRes ? 1 : 2;
        $illust->save();

        echo 'collection author illusts success';
    }


    /**
     * 获取作者所有作品
     * @param $illustID
     * @param string $nextURL
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    private function illustRelatedAll($illustID, $nextURL = "") {
        $data = $this->pixiv->illustRelated($illustID, $nextURL);

        if (!$data['illusts']) {
            Log::info("没有采集到此相关作品 , illust_id:" . $illustID);
            return false;
        }

        foreach ($data['illusts'] as $k => $v) {
            if ($v['total_bookmarks'] < SystemConfig::getConfig(SystemConfig::ILLUSTS_SAVE_CONDITION)) {
                continue;
            }

            //作者信息
            $author = \App\Models\Author::where('pixiv_id', $v['user']['id'])->exists();
            $authorCollected = 0;
            if ($author) {
                $authorCollected = 1;
            }

            if (!$this->saveIllusts($v, $authorCollected)) {
                Log::error("相关作品保存失败 , illust_id:" . $illustID, '; data:' . json_encode($v));
                return false;
            }

            if ($author) {
                continue;
            }

            $author = new \App\Models\Author();
            $author->pixiv_id = $v['user']['id'];
            $author->save();
        }

        if (!$data['next_url']) {
            return true;
        }

        $this->illustRelatedAll($illustID, $data['next_url']);
        return true;
    }

}