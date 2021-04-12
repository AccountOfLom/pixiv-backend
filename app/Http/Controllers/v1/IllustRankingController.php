<?php
/**
 * Created by PhpStorm.
 * User: shali
 * Date: 2021/4/13
 * Time: 1:03
 */

namespace App\Http\Controllers\v1;


use App\Admin\Repositories\SystemConfig;
use App\Http\Controllers\Controller;
use App\Models\IllustRanking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Predis\Client;

/**
 * 作品排行
 * Class IllustRankingController
 * @package App\Http\Controllers\v1
 */

class IllustRankingController extends Controller
{

    /**
     * r18 日排行
     * @return array
     * @throws \Throwable
     */
    public function r18Day()
    {
        //取日排行最新日期
        $date = Request::all('date');
        if (!$date) {
            $illust = IllustRanking::cacheFor(600)->where('mode', 'day_r18')->orderBy('date', 'desc')->first();
            if (!$illust) {
                return [];
            }
            $date = $illust->date;
        }

        $cacheKey = md5(Route::current()->uri . json_encode(Request::all()));

        $redis = new Client();
        $data = $redis->exists($cacheKey);
        if ($data) {
            return $this->success($redis->get($cacheKey));
        }

        $data = DB::table('illust_rankings as r')
            ->join('illustrations', 'r.pixiv_id', '=', 'illustrations.pixiv_id')
            ->leftJoin('illust_images', 'r.pixiv_id', '=', 'illust_images.illust_id')
            ->where(['r.mode' => 'day_r18', 'r.date' => $date, 'illustrations.image_collected' => 1])
            ->whereNotNULL('illust_images.square_medium_url')
            ->select(['r.id', 'r.pixiv_id', 'illust_images.square_medium_url'])
            ->groupBy('r.id')
            ->orderBy('r.id', 'asc')
            ->paginate();

        foreach ($data as $k => $v) {
            $data[$k]->square_medium_url = SystemConfig::getS3ResourcesURL($v->square_medium_url);
        }

        $redis->set($cacheKey, json_encode($data));
        $redis->expire($cacheKey, 600);
        return $this->success($data);
    }
}