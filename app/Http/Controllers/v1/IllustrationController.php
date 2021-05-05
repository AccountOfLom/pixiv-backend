<?php
/**
 * Created by PhpStorm.
 * User: shali
 * Date: 2021/4/13
 * Time: 1:03
 */

namespace App\Http\Controllers\v1;


use App\Admin\Repositories\Site;
use App\Cache\IllustCache;
use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\IllustRanking;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * 作品排行
 * Class IllustRankingController
 * @package App\Http\Controllers\v1
 */

class illustrationController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function tagsAndR18()
    {
        $r18Day = $this->r18Day();

        $ranking = [];
        $body = json_decode(json_encode($r18Day), true);
        foreach ($body['body']['data'] as $k => $v) {
            if ($k == 7) {  //首页排行榜显示7个作品
                break;
            }
            $ranking[] = $v;
        }

        $tags = Tag::where('is_recommend', 1)->orderBy('sort_num')->orderBy('id', 'desc')->select(['id', 'name', 'translated_name'])->get();

        return $this->success([
            'tags' => $tags,
            'ranking' => $ranking
        ]);

    }

    /**
     * r18 日排行
     * @return array
     * @throws \Throwable
     */
    public function r18Day()
    {
        $cacheData = $this->cacheData();
        if ($cacheData) {
            return $cacheData;
        }

        //取日排行最新日期
        $date = \Illuminate\Support\Facades\Request::all('date')['date'];
        if (!$date) {
            $illust = IllustRanking::cacheFor(600)->where('mode', 'day_r18')->orderBy('date', 'desc')->first();
            if (!$illust) {
                return [];
            }
            $date = $illust->date;
        }

        $data = DB::table('illust_rankings as r')
            ->join('illustrations', 'r.pixiv_id', '=', 'illustrations.pixiv_id')
            ->leftJoin('illust_images', 'r.pixiv_id', '=', 'illust_images.illust_id')
            ->where(['r.mode' => 'day_r18', 'r.date' => $date, 'illustrations.image_collected' => 1])
            ->whereNotNULL('illust_images.square_medium_url')
            ->select(['r.id', 'r.pixiv_id'])
            ->groupBy('r.id')
            ->orderBy('r.id', 'asc')
            ->paginate();

        foreach ($data as $k => $v) {
            $data[$k] = IllustCache::get($v->pixiv_id);
        }

        return $this->success($data, true);
    }


    public function list(Request $request)
    {
        $member = $request->session()->get('member');
        $cacheData = $this->cacheData();
        if ($cacheData) {
            $cacheData = json_decode(json_encode($cacheData), true);
            foreach ($cacheData['body']['data'] as &$v) {
                $v['is_bookmark'] = 0;
                if ($member) {
                    $isBookmark = Bookmark::validCache($member['id'], Bookmark::ILLUST, $v['id']);
                    if ($isBookmark) {
                        $v['is_bookmark'] = 1;
                    }
                }
            }
            return $cacheData;
        }

        $model = DB::table('illustrations as i')
            ->join('illust_images', 'i.pixiv_id', '=', 'illust_images.illust_id')
            ->where(['i.image_collected' => 1, 'illust_images.is_collected' => 1]);

        $site = Site::info($request->session()->get('site_id'));
        if ($site['x_restrict'] == 0) {
            $model->where('i.x_restrict', '0');
        }

        $model->select([ 'i.id', 'i.pixiv_id'])->groupBy('i.id');

        if ($request->get('is_login')) {
            //已登录随机排序
            $model->orderBy('sort_rand');
        } else {
            //未登录按收藏排序
            $model->orderBy('total_bookmarks', 'desc');
        }

        $data = $model->orderBy('i.id', 'desc')->paginate();

        foreach ($data as $k => $v) {
            $info = IllustCache::get($v->pixiv_id);
            $info['is_bookmark'] = 0;
            if ($member) {
                $isBookmark = Bookmark::validCache($member['id'], Bookmark::ILLUST, $v->id);
                if ($isBookmark) {
                    $info['is_bookmark'] = 1;
                }
            }
            $data[$k] = $info;
        }

        return $this->success($data, true);
    }
}