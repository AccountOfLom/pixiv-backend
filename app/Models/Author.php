<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Predis\Client;

class Author extends Model
{
	use HasDateTimeFormatter;
    use SoftDeletes;

    protected static function boot()
    {
        parent::boot();

        $client = new Client();

        static::saved(function ($author) use ($client) {
            $client->hset(\App\Admin\Repositories\Author::AUTHOR_CACHE_KEY, $author->pixiv_id, json_encode($author));
        });

        static::deleted(function ($author) use ($client) {
            $client->hdel(\App\Admin\Repositories\Author::AUTHOR_CACHE_KEY, $author->pixiv_id);
        });
    }
}
