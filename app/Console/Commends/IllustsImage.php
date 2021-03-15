<?php


namespace App\Console\Commends;


use App\Admin\Repositories\SystemConfig;
use App\Console\Common;
use App\Models\IllustImage;
use App\Models\Illustration;
use App\Server\Pixiv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * 作品图片
 * Class IllustsImage
 * @package App\Console\Commends
 */
class IllustsImage extends Command
{

    use Common;

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'illusts-image';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'illusts-image';


    /**
     * 执行控制台命令
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function handle()
    {
        //采集开关
        $switch = SystemConfig::getConfig(SystemConfig::DOWNLOAD_ILLUSTS_IMAGE_SWITCH);
        if (!$switch || $switch != SystemConfig::ENABLE) {
            echo '$switch' . $switch;
            return false;
        }

        //采集频率（分钟）
        $preTimeKey = "collection_illusts_image_time";
        $allow = $this->intervalAllow(SystemConfig::INTERVAL_DOWNLOAD_ILLUSTS_IMAGE, $preTimeKey);
        if (!$allow) {
            return false;
        }

        $images = (new IllustImage())->where("is_collected", 0)->first();
        if (!$images) {
            return false;
        }

        $illusts = (new Illustration())->where('pixiv_id', $images->illust_id)->first();

        if (!$images->square_medium_url) {
            $squareMediumURL = $this->imgDownloadAndUploadAndDel($images->p_square_medium_url);
            if (!$squareMediumURL) {
                Log::error("图片保存失败 ,p_square_medium_url:" . $images->p_square_medium_url);
                $images->is_collected = 2;
                $images->save();
                return false;
            }
            $images->square_medium_url = $squareMediumURL;
        }

        if (!$images->medium_url) {
            $mediumURL = $this->imgDownloadAndUploadAndDel($images->p_medium_url);
            if (!$mediumURL) {
                Log::error("图片保存失败 ,p_medium_url:" . $images->p_medium_url);
                $images->is_collected = 2;
                $images->save();
                return false;
            }
            $images->medium_url = $mediumURL;
        }

        if (!$images->large_url) {
            $largeURL = $this->imgDownloadAndUploadAndDel($images->p_large_url);
            if (!$largeURL) {
                Log::error("图片保存失败 ,p_large_url:" . $images->p_large_url);
                $images->is_collected = 2;
                $images->save();
                return false;
            }
            $images->large_url = $largeURL;
        }

        if (!$images->original_url) {
            $originalURL = $this->imgDownloadAndUploadAndDel($images->p_original_url);
            if (!$originalURL) {
                Log::error("图片保存失败 ,p_original_url:" . $images->p_original_url);
                $images->is_collected = 2;
                $images->save();
                return false;
            }
            $images->original_url = $originalURL;
        }

        //动画
        if ($illusts->type == \App\Admin\Repositories\Illustration::TYPE_UGOIRA && !$images->ugoira_zip_url) {
            $zipURL = $this->imgDownloadAndUploadAndDel($images->p_ugoira_zip_url);
            if (!$zipURL) {
                Log::error("zip保存失败 ,p_original_url:" . $images->p_ugoira_zip_url);
                $images->is_collected = 2;
                $images->save();
                return false;
            }
            $images->ugoira_zip_url = $zipURL;
        }

        $images->is_collected = 1;
        $images->collected_date = date('Y-m-d', time());
        $images->save();

        $illusts->image_collected = 1;
        $illusts->save();


        echo 'collection illust image success';
    }
}