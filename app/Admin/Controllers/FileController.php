<?php

namespace App\Admin\Controllers;

use App\Models\Paint;
use App\Server\Bucket\S3;
use Dcat\Admin\Traits\HasUploadedFile;
use Intervention\Image\Facades\Image;

/**
 * 文件
 * Class FileController
 * @package App\Admin\Controllers
 */
class FileController
{
    use HasUploadedFile;

    private function createFileName($basename)
    {
        return time() . uniqid() . $basename;
    }

    /**
     * 上传图片到S3
     * @return mixed|string
     * @throws \Throwable
     */
    public function image()
    {
        $file = $this->file();
        $fileMd5 = md5($file->getContent());
        $paint = Paint::where('file_md5', $fileMd5)->first();
        if ($paint) {
            return $this->responseUploaded($paint->url, $paint->url);
        }

        $fileName = $this->createFileName($file->getClientOriginalName());
        (new S3())->putObject($fileName, $file->getMimeType(), $file->getPath() . '/'. $file->getBasename());

        $thumbnail = $this->createThumbnail($file->getContent(), $fileName);
        (new S3())->putObject(self::getThumbnailIngName($fileName), $file->getMimeType(), $thumbnail);

        unlink($thumbnail);

        return $this->responseUploaded($fileName, $fileName);
    }

    /**
     * 生成缩略图
     * @param $imageContent
     * @param $baseURL
     * @return string
     */
    private function createThumbnail($imageContent, $baseURL)
    {
        if (!file_exists(public_path('uploads'))) {
            mkdir(public_path('uploads'));
        }

        $imagePath = public_path('uploads') . '/' . $baseURL;
        file_put_contents($imagePath, $imageContent);

        $image = Image::make($imagePath);

        $benchmark = 360;
        $w = $benchmark;
        $h = $benchmark;
        if ($image->width() < $image->height()) {
            $h = $image->height() * ($benchmark / $image->width());
        } else {
            $w = $image->width() * ($benchmark / $image->width());
        }

        $thumbnailImg = public_path('uploads') . '/' . self::getThumbnailIngName($baseURL);
        $image->resize($w, $h, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        })->crop($benchmark, $benchmark)->save($thumbnailImg);

        unlink($imagePath);

        return $thumbnailImg;
    }

    /**
     * 获取缩略图文件名
     * @param $baseURL
     * @return string
     */
    public static function getThumbnailIngName($baseURL)
    {
        return 'thumbnail_' . $baseURL;
    }
}
