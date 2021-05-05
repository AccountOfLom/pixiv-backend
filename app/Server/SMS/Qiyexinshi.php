<?php


namespace App\Server\SMS;

use App\Admin\Repositories\SystemConfig;
use GuzzleHttp\Client;

/**
 * 企业信使
 * http://121.36.217.169:8888/indexBlue.aspx
 * 0.3元/条
 * Class Qiyexinshi
 * @package App\Server\SMS
 */
class Qiyexinshi
{
    const CONF_KEY = 'sms_qyxs';

    const CODE = 'qyxs';

    private $conf;

    private $errMsg;

    public function __construct()
    {
        $conf = SystemConfig::getConfig(self::CONF_KEY);
        $this->conf = json_decode($conf, true);
    }

    private function sign($timestamp)
    {
        $str = $this->conf['account'] . $this->conf['password'] . $timestamp;
        return md5($str);
    }

    public function send($mobile, $content)
    {
        $timestamp = date("YmdHis");
        $url = 'http://121.36.217.169:8888/v2sms.aspx?action=send&rt=json&';

        $sign = $this->sign($timestamp);
        $param = "userid={$this->conf['id']}&timestamp={$timestamp}&sign={$sign}&mobile={$mobile}&content={$content}&sendTime=&extno=";

        $http = new Client();
        $response = $http->post($url . $param);

        $body = json_decode((string) $response->getBody(), true);
        if ($body['ReturnStatus'] != "Success") {
            $this->errMsg = json_encode($body);
            return false;
        }

        return $body['TaskID'];
    }

    public function getErrMsg()
    {
        return $this->errMsg;
    }

}