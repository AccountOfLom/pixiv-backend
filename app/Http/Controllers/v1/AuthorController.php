<?php


namespace App\Http\Controllers\v1;


use App\Admin\Repositories\SystemConfig;
use App\Cache\IllustCache;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Follow;
use App\Models\Illustration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * 作者
 * Class AuthorController
 * @package App\Http\Controllers\v1
 */
class AuthorController extends Controller
{
    public function details(Request $request)
    {
        $pixivID = $request->input('pixiv_id');
        if (!$pixivID) {
            return $this->error('pixiv_id 不能为空');
        }
        $author = Author::where('pixiv_id', $pixivID)
            ->select(['id', 'pixiv_id', 'name', 'account', 'profile_image_url', 'total_follow_users', 'comment', 'webpage', 'region', 'total_follow_users', 'background_image_url', 'twitter_url'])
            ->first();
        if (!$author) {
            return $this->error('作者信息不存在');
        }
        $result = $author->toArray();
        $result['profile_image_url'] = SystemConfig::getS3ResourcesURL($result['profile_image_url']);
        $result['background_image_url'] = SystemConfig::getS3ResourcesURL($result['background_image_url']);
        $result['is_follow'] = 0;
        $member = $request->session()->get('member');
        if ($member) {
            $isFollow = Follow::where(['member_id' => $member['id'], 'pixiv_id' => $pixivID])->exists();
            if ($isFollow) {
                $result['is_follow'] = 1;
            }
        }
        return $this->success($result);
    }

    public function follow(Request $request)
    {
        $memberID = $member = $request->session()->get('member')['id'];
        $pixivID = $request->input('pixiv_id');
        if (!$pixivID) {
            return $this->error('pixiv_id 不能为空');
        }
        $followed = Follow::where(['member_id' => $memberID, 'pixiv_id' => $pixivID])->exists();
        if ($followed) {
            return $this->success();
        }

        $follow = new Follow();
        $follow->member_id = $memberID;
        $follow->pixiv_id = $pixivID;
        $follow->save();

        Author::where('pixiv_id', $pixivID)->increment('total_follow_users');

        return $this->success();
    }

    public function unFollow(Request $request)
    {
        $memberID = $member = $request->session()->get('member')['id'];
        $pixivID = $request->input('pixiv_id');
        if (!$pixivID) {
            return $this->error('pixiv_id 不能为空');
        }

        Follow::where(['member_id' => $memberID, 'pixiv_id' => $pixivID])->delete();

        Author::where('pixiv_id', $pixivID)->decrement('total_follow_users');

        return $this->success();
    }

    /**
     * 关注列表
     * @param Request $request
     * @return array
     */
    public function followList(Request $request)
    {
        $memberID = $member = $request->session()->get('member')['id'];
        $authors = DB::table('follows as f')
            ->leftJoin('authors', 'f.pixiv_id', '=', 'authors.pixiv_id')
            ->where('f.member_id', $memberID)
            ->select(['f.pixiv_id', 'authors.name', 'profile_image_url'])
            ->orderBy('f.id', 'desc')
            ->paginate()
            ->toArray();

        if ($authors['total'] == 0) {
            return $this->success();
        }

        $data = json_decode(json_encode($authors), true);
        foreach ($data['data'] as &$v) {
            $v['profile_image_url'] = SystemConfig::getS3ResourcesURL($v['profile_image_url']);
            $illust = Illustration::where('author_pixiv_id', $v['pixiv_id'])->orderBy('id', 'desc')->select(['pixiv_id'])->limit(3)->get();
            $v['illust'] = [];
            if ($illust) {
                foreach ($illust as $i) {
                    array_push($v['illust'], IllustCache::get($i->pixiv_id));
                }
            }
        }

        return $this->success($data);
    }
}