<?php


namespace App\Console\Commends;


use App\Admin\Repositories\SystemConfig;
use App\Console\Common;
use App\Models\IllustImage;
use App\Models\IllustRanking;
use App\Server\Pixiv;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * R18作品日排行
 * Class Ranging
 * @package App\Console\Commends
 */
class RankingDay extends Command
{

    use Common;

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'ranking-day';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'ranking-day';

    protected $mode = 'day_r18';

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
        $switch = SystemConfig::getConfig(SystemConfig::RANKING_DAY_SWITCH);
        if (!$switch || $switch != SystemConfig::ENABLE) {
            return false;
        }

        //昨日的排行
        $date = date("Y-m-d", strtotime(' -1 day'));
        $collected = (new IllustRanking())->where("date", $date)->exists();
        if ($collected) {
            return false;
        }

        //作品列表
        $this->getRanking($this->mode, $date);

        echo 'collection illust ranking success';
    }

    /**
     * 获取排行作品
     * @param $mode
     * @param $date
     * @param string $nextURL
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    private function getRanking($mode, $date, $nextURL = "") {
        $data = $this->pixiv->illustRanking($mode, $date, $nextURL);
        dd($data);

        if (!$data['illusts']) {
            Log::info("没有采集到排行作品 , mode:" . $mode . '; date:' . $date);
            return false;
        }

        foreach ($data['illusts'] as $k => $v) {
            if (!$this->saveIllusts($v)) {
                Log::error("排行作品保存失败 , data:" . json_encode($v));
                return false;
            }

            (new IllustImage())->where("illust_id", $v['id'])->update(['is_priority_collect' => 1]);

            $ranking = new IllustRanking();
            $ranking->pixiv_id = $v['id'];
            $ranking->mode = $this->mode;
            $ranking->date = $date;
            $ranking->save();
        }

        if (!$data['next_url']) {
            return true;
        }

        $this->getRanking($mode, $date, $data['next_url']);
        return true;
    }

}