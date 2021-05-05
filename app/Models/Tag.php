<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;
use Predis\Client;

class Tag extends Model
{
	use HasDateTimeFormatter;    


    protected static function boot()
    {
        parent::boot();

        $client = new Client();

        static::saved(function ($tag) use ($client) {
            $client->hset(\App\Admin\Repositories\Tag::TAG_CACHE_KEY, $tag->name, json_encode($tag));
            $client->hset(\App\Admin\Repositories\Tag::TAG_CACHE_KEY, $tag->id, json_encode($tag));
        });

        static::deleted(function ($tag) use ($client) {
            $client->hdel(\App\Admin\Repositories\Tag::TAG_CACHE_KEY, json_encode($tag));
            $client->hdel(\App\Admin\Repositories\Tag::TAG_CACHE_KEY, json_encode($tag));
        });
    }
}
