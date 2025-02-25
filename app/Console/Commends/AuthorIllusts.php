<?php


namespace App\Console\Commends;


use App\Admin\Repositories\SystemConfig;
use App\Console\Common;
use App\Server\Pixiv;
use Illuminate\Console\Command;
use App\Models\Author as AuthorModel;
use Illuminate\Support\Facades\Log;

/**
 * 作者的作品列表
 * Class AuthorIllusts
 * @package App\Console\Commends
 */
class AuthorIllusts extends Command
{

    use Common;

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'author-illusts';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'author-illusts';

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
        $switch = SystemConfig::getConfig(SystemConfig::AUTHOR_ILLUSTS_SWITCH);
        if (!$switch || $switch != SystemConfig::ENABLE) {
            return false;
        }

        //采集频率（分钟）
        $preTimeKey = "collection_author_illusts_time";
        $allow = $this->intervalAllow(SystemConfig::P_INTERVAL_AUTHOR_ILLUSTS, $preTimeKey);
        if (!$allow) {
            return false;
        }

        $author = (new AuthorModel())->where("is_collected_illust", 0)->orderBy("is_priority_collect", "desc")->first();
        if (!$author) {
            return false;
        }

        //作品列表
        $saveRes = $this->getUserIllusts($author->pixiv_id);
        if (!$saveRes) {
            $author->is_collected_illust = 2;
            $author->save();
            return false;
        }

        $author->is_collected_illust = 1;
        $author->collected_illust_date = date('Ymd', time());
        $author->save();

        echo 'collection author illusts success';
    }

    /**
     * 获取作者所有作品
     * @param $authorID
     * @param string $nextURL
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    private function getUserIllusts($authorID, $nextURL = "") {
        $data = $this->pixiv->userIllusts($authorID, $nextURL);

        if (!$data['illusts']) {
            Log::info("没有采集到此作者的作品 , pixiv_id:" . $authorID);
            return false;
        }

        foreach ($data['illusts'] as $k => $v) {
            if ($v['total_bookmarks'] < SystemConfig::getConfig(SystemConfig::ILLUSTS_SAVE_CONDITION)) {
                continue;
            }
            if (!$this->saveIllusts($v)) {
                Log::error("作者的作品保存失败 , pixiv_id:" . $authorID, '; data:' . json_encode($v));
                return false;
            }
        }

        if (!$data['next_url']) {
            return true;
        }

        $this->getUserIllusts($authorID, $data['next_url']);
        return true;
    }

}