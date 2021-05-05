<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Request;

class Site
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
            'code' => 1,
            'msg' => '',
            'body' => []
        ];
        if (!Request::hasHeader('new-referer')) {
            $errRes['msg'] = 'site no find';
            die(json_encode($errRes));
        }

        $domain = \App\Admin\Repositories\Domain::info(Request::header('new-referer'));

        if (!$domain) {
            $errRes['msg'] = 'domain no find';
            die(json_encode($errRes));
        }

        if ($domain['status'] != 1) {
            $errRes['msg'] = 'domain disable';
            die(json_encode($errRes));
        }

        $site = \App\Admin\Repositories\Site::info($domain['site_id']);

        if ($site['status'] != 1) {
            $errRes['msg'] = 'site closed';
            die(json_encode($errRes));
        }

        $isLogin = ['is_login' => 0];
        if (Request::hasHeader('token') && strlen(Request::header('token')) > 0) {
            $isLogin = ['is_login' => 1];
        }
        $request->attributes->add($isLogin);

        $request->session()->put('site_id', $site['id']);
        $request->session()->put('site_r18', $site['x_restrict']);

        return $next($request);
    }
}
