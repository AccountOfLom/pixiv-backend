<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;
use Predis\Client;

class Token
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $errRes = [
            'code' => 4001,
            'msg' => '请先登录',
            'body' => []
        ];
        if (!Request::hasHeader('token')) {
            $errRes['msg'] = 'token no find';
            die(json_encode($errRes));
        }

        $member = \App\Server\Token::valid(Request::header('token'));

        if (!$member) {
            $errRes['code'] = 4001;
            $errRes['msg'] = '登录凭证无效';
            die(json_encode($errRes));
        }

        if ($member['state'] != 1) {
            $errRes['msg'] = '账号已冻结';
            die(json_encode($errRes));
        }

        $request->session()->put('member', $member);

        return $next($request);
    }
}
