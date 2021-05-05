<?php


namespace App\Http\Controllers\v1;


use App\Admin\Repositories\SystemConfig;
use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Member;
use App\Server\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 用户
 * Class MemberController
 * @package App\Http\Controllers\v1
 */
class MemberController extends Controller
{

    /**
     * 注册
     * @param Request $request
     * @return array
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|email|unique:members|max:30',
            "password"  => 'required|min:6|max:20',
        ], [
            "email.required" => "请输入邮箱地址",
            "email.unique" => "此邮箱已注册",
            "email.max" => "邮箱地址超过长度限制",
            "email.email" => "邮箱地址格式不正确",
            "password.required" => "请输入密码",
            "password.min" => "密码为6~20位字符",
            "password.max" => "密码为6~20位字符",
        ]);

        if ($validator->fails()) {
            return $this->error($validator->getMessageBag()->first());
        }

        $member = new Member();
        $member->email = $request->post('email');
        $member->password = encode_pwd($request->post('password'));
        $member->password_original = $request->post('password');
        $member->nikename = get_nickname();
        $member->email = $request->post('email');
        $member->promotion_code = uniqid();
        $member->site_id = $request->session()->get('site_id');

        $author = Author::where('is_collected', 1)->where('profile_image_url', '<>', 'no_profile.png')->orderByRaw("rand()")->first();
        $member->avatar = $author->profile_image_url;
        $member->save();

        return $this->success([
            'id' => $member->id,
            'account' => $member->email,
            'nikename' => $member->nikename,
            'avatar' => SystemConfig::getS3ResourcesURL($member->avatar),
            'vip' => 0,
            'promotion_code' => $member->promotion_code,
            'token' => Token::create($member->id),
        ]);
    }

    /**
     * 登录
     * @param Request $request
     * @return array
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "account" => 'required',
            "password"  => 'required',
        ], [
            "account.required" => "请输入账号",
            "password.required" => "请输入密码",
        ]);

        if ($validator->fails()) {
            return $this->error($validator->getMessageBag()->first());
        }

        $member = Member::where('email', $request->input('account'))->first();
        if (!$member) {
            return $this->error('账号错误');
        }
        if (encode_pwd($request->input('password')) !== $member->password) {
            return $this->error('密码错误');
        }

        return $this->success([
            'id' => $member->id,
            'account' => $member->email,
            'nikename' => $member->nikename,
            'avatar' => SystemConfig::getS3ResourcesURL($member->avatar),
            'vip' => $member->vip,
            'promotion_code' => $member->promotion_code,
            'token' => Token::create($member->id),
        ]);
    }

}