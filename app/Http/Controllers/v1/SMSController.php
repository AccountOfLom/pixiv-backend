<?php


namespace App\Http\Controllers\v1;


use App\Http\Controllers\Controller;
use App\Server\SMS\Action;
use Illuminate\Support\Facades\Request;

class SMSController extends Controller
{

    /**
     * 发送验证码
     * @return array
     */
    public function sendVerificationCode()
    {
//        var_dump(session('site_id'));
//        die;
        $phoneNumber = Request::input('phoneNumber');
        if (!$phoneNumber) {
            return $this->error('请输入手机号');
        }

        $g = "/^1[34578]\d{9}$/";
        if(!preg_match($g, $phoneNumber)) {
            return $this->error('请输入正确的手机号');
        }

        $sendRes = (new Action())->sendVerificationCode($phoneNumber);
        if ($sendRes !== true) {
            return $this->error($sendRes);
        }

        return $this->success();
    }
}