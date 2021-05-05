<?php


namespace App\Http\Controllers\v1;


use App\Admin\Repositories\SystemConfig;
use App\Admin\Repositories\Tag;
use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Paint;
use Illuminate\Http\Request;

class PaintController extends Controller
{
    /**
     * @param Request $request
     * @return array|null
     * @throws \Throwable
     */
    public function list(Request $request)
    {
        $cacheData = $this->cacheData();
        if ($cacheData) {
            return $cacheData;
        }

        $paints = Paint::orderBy('id', 'desc')->paginate()->toArray();

        $member = $request->session()->get('member');
        foreach ($paints['data'] as $k => &$v) {
            $v['url'] = SystemConfig::getS3ResourcesURL($v['url']);
            $v['thumbnail'] = SystemConfig::getS3ResourcesURL($v['thumbnail']);
            $v['is_bookmark'] = 0;
            if ($member) {
                $isBookmark = Bookmark::validCache($member['id'], Bookmark::ANIME, $v['id']);
                if ($isBookmark) {
                    $v['is_bookmark'] = 1;
                }
            }
            //标签
            $tags = [];
            $tagIDs = explode(',', $v['tag_ids']);
            foreach ($tagIDs as $tagID) {
                $tag = (new Tag())->getTagByID($tagID);
                if ($tag) {
                    $tags[] = $tag;
                }
            }
            $v['tags'] = $tags;
        }

        return $this->success($paints, true);
    }
}