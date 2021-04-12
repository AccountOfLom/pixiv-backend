<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function success($data)
    {
        if (is_string($data)) {
            $decodeData = json_decode($data);
            if ($decodeData) {
                $data = $decodeData;
            }
        }
        return [
            'code' => 0,
            'message' => 'success',
            'data' => $data
        ];
    }

    public function error($msg = '', $data = '')
    {
        return [
            'code' => 1,
            'message' => $msg,
            'data' => $data
        ];
    }
}
