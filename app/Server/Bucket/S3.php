<?php
/**
 * Created by PhpStorm.
 * User: shali
 * Date: 2021/3/12
 * Time: 18:01
 */

namespace App\Server\Bucket;


use App\Admin\Repositories\SystemConfig;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class S3
{
    protected $client;

    protected $bucket;

    function __construct()
    {
        $cfgStr = SystemConfig::getConfig(SystemConfig::S3);
        if (!$cfgStr) {
            Log::error("S3配置获取失败,key:" . SystemConfig::S3);
            return false;
        }

        $cfg = json_decode($cfgStr);
        if (!$cfg) {
            Log::error("S3配置数据格式化失败,cfg data:" . $cfgStr);
            return false;
        }
        $this->client = S3Client::factory(array(
            "credentials" => [
                "key" => $cfg->key,
                "secret" => $cfg->secret,
            ],
            "region" => $cfg->region,
            "scheme" => $cfg->scheme,
            "version" => "latest",
        ));

        if (!$this->client) {
            Log::error("S3Client 初始化失败,cfg data:" . $cfgStr);
            return false;
        }

        $this->bucket = $cfg->bucket;
    }

    /**
     * 上传文件
     * @param $fileName
     * @param string $contentType
     * @param $filePath
     * @return bool|mixed
     */
    public function putObject($fileName, $contentType = '', $filePath) {
        $conf = [
            "Bucket" => $this->bucket,
            "Key" => $fileName,
            "Body" => file_get_contents($filePath)
        ];
        if ($contentType) {
            $conf['ContentType'] = $contentType;
        }
        $result = $this->client->putObject($conf);
        if (!$result['ObjectURL']) {
            Log::error("文件上传至S3失败:" . json_encode($result));
            return false;
        }
        return $result['ObjectURL'];
    }

    /**
     * 删除文件
     * @param $fileName
     */
    public function deletedObject($fileName)
    {
        $this->client->deleteObject(array(
            "Bucket" => $this->bucket,
            "Key" => $fileName
        ));
    }



}