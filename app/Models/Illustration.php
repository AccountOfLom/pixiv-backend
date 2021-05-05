<?php

namespace App\Models;

use App\Server\Bucket\S3;
use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class Illustration extends Model
{
	use HasDateTimeFormatter;

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($illust) {
            $images = IllustImage::where('illust_id', $illust->pixiv_id)->get();

            //TODO 批量删除删除图片
//            $s3 = new S3();
            foreach ($images as $image) {
//                $s3->deletedObject($image->square_medium_url);
//                $s3->deletedObject($image->medium_url);
//                $s3->deletedObject($image->large_url);
//                $s3->deletedObject($image->original_url);
//                $s3->deletedObject($image->ugoira_zip_url);
                $image->delete();
            }
        });
    }
}
