<?php


namespace App\Server\SMS;


use App\Models\SmsLog;
use Predis\Client;

class Action
{
    private $cacheKey = 'sms_';

    //过期时间
    private $expire = 300;

    //发送间隔
    private $interval = 60;

    public function sendVerificationCode($phoneNumber)
    {
        $r = new Client();
        $intervalKey = 'sms_interval_' . $phoneNumber;
        if ($r->exists($intervalKey)) {
            return "短信发送间隔:{$this->interval}秒";
        }

        $code = rand( 1000, 9999);
        $content = "【次元网】您的验证码是{$code}，5分钟内有效，请勿泄露给他人。";

        $sendRes = (new Qiyexinshi())->send($phoneNumber, $content);

        if (!$sendRes) {
            return '短信发送失败，请检查手机号是否正确后重试，或选择邮箱注册！';
        }

        $r->set($this->cacheKey . $phoneNumber, $code, 'ex', $this->expire);

        $r->set($intervalKey, true, 'ex', $this->interval);

        $this->saveLog(Qiyexinshi::CODE, $sendRes['order_id']);

        return true;
    }

    public function validVerificationCode($phoneNumber, $code)
    {
        $r = new Client();
        $key = $this->cacheKey . $phoneNumber;
        if (!$r->exists($key)) {
            return false;
        }

        $cacheCode = $r->get($key);
        if ($cacheCode != $code) {
            return false;
        }

        $r->del($key);
        return true;
    }


    private function saveLog($channelCode, $orderID)
    {
        $log = (new SmsLog());
        $log->channel_code = $channelCode;
        $log->order_id = $orderID;
        $log->created_at = now();
        $log->save();
    }

}