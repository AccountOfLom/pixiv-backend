<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Predis\Client;

class Site extends Model
{
	use HasDateTimeFormatter;
    use SoftDeletes;


    protected static function boot()
    {
        parent::boot();

        $client = new Client();

        static::saved(function ($site) use ($client) {
            $client->set(\App\Admin\Repositories\Site::CACHE_KEY . $site->id, json_encode($site));
        });

        static::deleted(function ($site) use ($client) {
            $client->del(\App\Admin\Repositories\Site::CACHE_KEY . $site->id);
        });
    }
}
