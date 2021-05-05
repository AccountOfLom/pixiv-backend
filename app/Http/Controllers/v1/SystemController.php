<?php


namespace App\Http\Controllers\v1;


use App\Admin\Repositories\Site;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function config(Request $request)
    {
        $config = Site::info($request->session()->get('site_id'));
        return $this->success(['x_restrict' => $config['x_restrict']]);
    }
}