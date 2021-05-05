<?php


namespace App\Http\Controllers\v1;


use App\Admin\Repositories\SystemConfig;
use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\AnimeGroup;
use App\Models\Bookmark;
use Illuminate\Http\Request;

/**
 * Class AnimeController
 * @package App\Http\Controllers\v1
 */
class AnimeController extends Controller
{
    /**
     * @param Request $request
     * @return array|null
     * @throws \Throwable
     */
    public function list(Request $request)
    {
        $member = $request->session()->get('member');
        $cacheData = $this->cacheData();
        if ($cacheData) {
            $cacheData = json_decode(json_encode($cacheData), true);
            foreach ($cacheData['body']['data'] as $k => &$v) {
                $v['is_bookmark'] = 0;
                if ($member) {
                    $isBookmark = Bookmark::validCache($member['id'], Bookmark::ANIME, $v['id']);
                    if ($isBookmark) {
                        $v['is_bookmark'] = 1;
                    }
                }
                if ($v['group']) {
                    foreach ($v['group'] as &$vv) {
                        $vv['is_bookmark'] = 0;
                        if ($member) {
                            $isBookmark = Bookmark::validCache($member['id'], Bookmark::ANIME, $vv['id']);
                            if ($isBookmark) {
                                $vv['is_bookmark'] = 1;
                            }
                        }
                    }
                }
            }
            return $cacheData;
        }

        $animes = Anime::orderBy('id', 'desc')->select(['id', 'title', 'url', 'image', 'group_id', 'created_at'])->paginate()->toArray();

        foreach ($animes['data'] as $k => &$v) {
            $v['url'] = SystemConfig::getS3ResourcesURL($v['url']);
            $v['image'] = SystemConfig::getS3ResourcesURL($v['image']);
            $v['is_bookmark'] = 0;
            if ($member) {
                $isBookmark = Bookmark::validCache($member['id'], Bookmark::ANIME, $v['id']);
                if ($isBookmark) {
                    $v['is_bookmark'] = 1;
                }
            }
            $v['group_name'] = $v['group_id'] == 0 ? '' : AnimeGroup::where('id', $v['group_id'])->value('name');
            $v['group'] = $this->getGroup($v['group_id'], $v['id'], $member);
        }

        return $this->success($animes, true);
    }

    /**
     * 系列
     * @param $groupID
     * @param $currentID
     * @param $member
     * @return array
     * @throws \Throwable
     */
    public function getGroup($groupID, $currentID, $member)
    {
        if ($groupID == 0) {
            return [];
        }
        $animes = Anime::where('group_id', $groupID)->where('id', '<>', $currentID)->select(['id', 'title', 'url', 'image', 'group_id', 'created_at'])->orderBy('id', 'desc')->get();
        if (!$animes) {
            return [];
        }
        $animes = $animes->toArray();
        foreach ($animes as $k => $v) {
            $animes[$k]['group_name'] = $v['group_id'] == 0 ? '' : AnimeGroup::where('id', $v['group_id'])->value('name');
            $animes[$k]['url'] = SystemConfig::getS3ResourcesURL($v['url']);
            $animes[$k]['image'] = SystemConfig::getS3ResourcesURL($v['image']);
            $animes[$k]['is_bookmark'] = 0;
            if ($member) {
                $isBookmark = Bookmark::validCache($member['id'], Bookmark::ANIME, $v['id']);
                if ($isBookmark) {
                    $animes[$k]['is_bookmark'] = 1;
                }
            }
        }
        return $animes;
    }


}