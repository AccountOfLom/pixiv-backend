<?php


namespace App\Http\Controllers\v1;


use App\Admin\Repositories\SystemConfig;
use App\Admin\Repositories\Tag;
use App\Cache\IllustCache;
use App\Http\Controllers\Controller;
use App\Models\Anime;
use App\Models\Bookmark;
use App\Models\Paint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Predis\Client;

class BookmarkController extends Controller
{
    /**
     * 收藏列表
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function list(Request $request)
    {
        $bookmarks = Bookmark::where('member_id', $request->session()->get('member')['id'])->orderBy('id', 'desc')->paginate()->toArray();
        foreach ($bookmarks['data'] as &$v) {
            $content = "";
            switch ($v['type']){
                case Bookmark::ILLUST:
                    $pixivID = IllustCache::IDMapping($v['content_id']);
                    $content = IllustCache::get($pixivID);
                    $thumbnail = current($content['images'])['square_medium_url'];
                    break;
                case Bookmark::ANIME:
                    $anime = Anime::find($v['content_id']);
                    if (!$anime) {
                        break;
                    }
                    $content = $anime->toArray();
                    $content['url'] = SystemConfig::getS3ResourcesURL($content['url']);
                    $content['image'] = SystemConfig::getS3ResourcesURL($content['image']);
                    $thumbnail = $content['image'];
                    $content['group'] = (new AnimeController())->getGroup($content['group_id'], $content['id'], false);
                    break;
                case Bookmark::PAINT:
                    $paint = Paint::find($v['content_id']);
                    if (!$paint) {
                        break;
                    }
                    $content = $paint->toArray();
                    $content['url'] = SystemConfig::getS3ResourcesURL($content['url']);
                    $content['thumbnail'] = SystemConfig::getS3ResourcesURL($content['thumbnail']);
                    $thumbnail = $content['thumbnail'];
                    //标签
                    $tags = [];
                    $tagIDs = explode(',', $content['tag_ids']);
                    foreach ($tagIDs as $tagID) {
                        $tag = (new Tag())->getTagByID($tagID);
                        if ($tag) {
                            $tags[] = $tag;
                        }
                    }
                    $content['tags'] = $tags;
                    break;
                default:
                    break;
            }

            if (!$content) {
                continue;
            }
            $v['thumbnail'] = $thumbnail;
            $v['content'] = $content;
        }

        return $this->success($bookmarks);
    }

    /**
     * 添加收藏
     * @param Request $request
     * @return array
     * @throws \Throwable
     */
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "content_id" => 'required',
            "type"  => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->getMessageBag()->first());
        }

        $content = false;
        switch ($request->input('type')){
            case Bookmark::ILLUST:
                $pixivID = IllustCache::IDMapping($request->input('content_id'));
                if (!$pixivID) {
                    break;
                }
                $content = IllustCache::get($pixivID);
                break;
            case Bookmark::ANIME:
                $content = Anime::find($request->input('content_id'));
                break;
            case Bookmark::PAINT:
                $content = Paint::find($request->input('content_id'));
                break;
            default:
                break;
        }

        if (!$content) {
            return $this->error('资源不存在');
        }

        $memberID = $request->session()->get('member')['id'];
        $cacheKey = Bookmark::CACHE_KEY . $memberID;
        $childKey = $request->input('type') . '_' . $request->input('content_id');
        $client = new Client();
        $exist = $client->hexists($cacheKey, $childKey);
        if ($exist) {
            return $this->success();
        }

        $bookmark = new Bookmark();
        $bookmark->member_id = $memberID;
        $bookmark->type = $request->input('type');
        $bookmark->content_id = $request->input('content_id');
        $bookmark->created_at = now();
        $bookmark->save();

        $client->hset($cacheKey, $childKey, 1);

        return $this->success();
    }


    /**
     * 取消收藏
     * @param Request $request
     * @return array
     */
    public function del(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "content_id" => 'required',
            "type"  => 'required|in:1,2,3',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->getMessageBag()->first());
        }

        $memberID = $request->session()->get('member')['id'];

        $cacheKey = Bookmark::CACHE_KEY . $memberID;
        $childKey = $request->input('type') . '_' . $request->input('content_id');
        $client = new Client();
        $exist = $client->hexists($cacheKey, $childKey);
        if (!$exist) {
            return $this->success();
        }
        $client->hdel($cacheKey, [$childKey]);

        Bookmark::where([
            'member_id' => $memberID,
            'content_id' => $request->input('content_id'),
            'type' => $request->input('type')])
            ->delete();

        return $this->success();
    }
}