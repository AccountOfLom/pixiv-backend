<?php
/**
 * Created by PhpStorm.
 * User: shali
 * Date: 2021/3/13
 * Time: 18:37
 */

namespace App\Server;


use App\Admin\Repositories\SystemConfig;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Pixiv
{
    protected $httpClient;

    protected $domain;

    //文件保存地址
    public $imageDownloadPath;

    /**
     * Pixiv constructor.
     * @throws \Throwable
     */
    function __construct()
    {
        $this->httpClient = new Client();
        $this->domain = SystemConfig::getConfig(SystemConfig::PIXIV_API_URL);
        if ($this->domain === null) {
            return false;
        }
        $this->imageDownloadPath = base_path();
    }

    /**
     * 作者信息
     * @param $pixivID
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function userDetail($pixivID) {
        $response = $this->httpClient->request('GET', $this->domain . '/user_detail?user_id=' . $pixivID);
        return $this->formatResponse($response);
    }


    /**
     * 作者作品列表
     * @param $pixivID
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function userIllusts($pixivID) {
        $response = $this->httpClient->request('GET', $this->domain . '/user_illusts?user_id=' . $pixivID);
        return $this->formatResponse($response);
    }


    /**
     * 动画zip
     * @param $illustsID
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function ugoiraMetadata($illustsID) {
        $response = $this->httpClient->request('GET', $this->domain . '/ugoira_metadata?illust_id=' . $illustsID);
        return $this->formatResponse($response);
    }


    /**
     * 下载图片
     * @param $imageURL
     * @param $downloadPath
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function imageDownload($imageURL, $downloadPath) {
        $response = $this->httpClient->request('GET', $this->domain . '/image_download?image_url=' . $imageURL . '&path=' . $downloadPath);
        return $this->formatResponse($response);
    }

    /**
     * 获取文件名
     * @param $imageURL
     * @return mixed
     */
    public function getFileBaseName($imageURL) {
        $info = explode('/', $imageURL);
        return $info[count($info)-1];
    }


    /**
     * 校验&格式化
     * @param $response
     * @return bool|mixed
     */
    protected function formatResponse($response) {
        if (!$response) {
            Log::error("采集请求错误");
            return false;
        }
        if ($response->getStatusCode() != 200) {
            Log::error("数据采集失败，StatusCode：" . $response->getStatusCode(). ";content:" . $response->getBody()->getContents());
            return false;
        }

        $content = $response->getBody()->getContents();

        //图片下载返回数据
        if ($content == 'true' || $content == 'false') {
            return $content == 'true';
        }

        $response = json_decode($content, true);
        if (!is_array($response)) {
            Log::error("采集数据格式化失败：" . $content);
            return false;
        }
        return $response;
    }

}