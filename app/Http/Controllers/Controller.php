<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Predis\Client;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $dataCacheKey;

    protected $expireTime = 600;

    protected function cacheKey()
    {
        $params = Request::all();
        $params['is_login'] = Request::get('is_login');
        $this->dataCacheKey = md5(Route::current()->uri . json_encode($params));
    }

    protected function cacheData()
    {
        $this->cacheKey();

        $redis = new Client();
        $data = $redis->exists($this->dataCacheKey);
        if ($data) {
            return $this->success($redis->get($this->dataCacheKey));
        }
        return null;
    }


    protected function success($data = [], $cache = false)
    {
        if ($cache) {
            if (!$this->dataCacheKey) {
                new \Exception('未设置缓存key, cacheKey 未被调用');
            }
            $redis = new Client();
            if (is_array($data) || is_object($data)) {
                $redis->set($this->dataCacheKey, json_encode($data));
            } else {
                $redis->set($this->dataCacheKey, $data);
            }
            $redis->expire($this->dataCacheKey, $this->expireTime);
        }

        if (is_string($data)) {
            $decodeData = json_decode($data);
            if ($decodeData) {
                $data = $decodeData;
            }
        }

        return [
            'code' => 0,
            'message' => 'success',
            'body' => $data
        ];
    }

    protected function error($msg = '', $data = [])
    {
        return [
            'code' => 1,
            'message' => $msg,
            'body' => $data
        ];
    }
}
