<?php


namespace App\Console\Commends;


use App\Admin\Repositories\SystemConfig;
use App\Console\Common;
use App\Server\Pixiv;
use Illuminate\Console\Command;
use App\Models\Author as AuthorModel;
use Illuminate\Support\Facades\Log;

/**
 * 作者信息
 * Class Author
 * @package App\Console\Commends
 */
class Author extends Command
{

    use Common;

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'author';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'author';


    /**
     * 执行控制台命令
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function handle()
    {
        //作者信息采集开关
        $switch = SystemConfig::getConfig(SystemConfig::P_AUTHOR_SWITCH);
        if (!$switch || $switch != SystemConfig::ENABLE) {
            return false;
        }

        //采集频率（分钟）
        $preTimeKey = "collection_author_time";
        $allow = $this->intervalAllow(SystemConfig::P_INTERVAL_AUTHOR, $preTimeKey);
        if (!$allow) {
            return false;
        }

        $author = (new AuthorModel())->where("is_collected", 0)->orderBy("is_priority_collect", "desc")->first();
        if (!$author) {
            return false;
        }

        //作者信息
        $pixiv = new Pixiv();
        $authorInfo = $pixiv->userDetail($author->pixiv_id);

        //头像
        $profileURL = $authorInfo['user']['profile_image_urls']['medium'];
        $profileImg = $this->imgDownloadAndUploadAndDel($profileURL);
        if (!$profileImg) {
            Log::error("作者头像保存失败 ,profile_image_url:" . $profileURL);
            return false;
        }

        //主页背景
        $bgImgURL = $authorInfo['profile']['background_image_url'];
        $bgImg = $this->imgDownloadAndUploadAndDel($bgImgURL);
        if (!$bgImg) {
            Log::error("作者主页背景图保存失败 ,background_image_url:" . $bgImgURL);
            return false;
        }

        try {
            $author->name                   = $authorInfo['user']['name'];
            $author->account                = $authorInfo['user']['account'];
            $author->profile_image_url      = $profileImg['url'];
            $author->p_profile_image_url    = $authorInfo['user']['profile_image_urls']['medium'];
            $author->comment                = $authorInfo['user']['comment'];
            $author->webpage                = $authorInfo['profile']['webpage'];
            $author->region                 = $authorInfo['profile']['region'];
            $author->country_code           = $authorInfo['profile']['country_code'];
            $author->total_follow_users     = $authorInfo['profile']['total_follow_users'];
            $author->total_mypixiv_users    = $authorInfo['profile']['total_mypixiv_users'];
            $author->background_image_url   = $bgImg;
            $author->p_background_image_url = $authorInfo['profile']['background_image_url'];
            $author->twitter_account        = $authorInfo['profile']['twitter_account'];
            $author->twitter_url            = $authorInfo['profile']['twitter_url'];
            $author->is_collected           = \App\Admin\Repositories\Author::COLLECTED;
            $author->collected_date         = date("Y-m-d", time());
            $author->is_collected_illust    = 0;
            $author->save();
        } catch (\Exception $e) {
            Log::error("作者信息更新失败,line:" . $e->getLine() . '; Message:' . $e->getMessage());
            return false;
        }

        echo 'collection author success';
    }
}