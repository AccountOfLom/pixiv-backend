<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Predis\Client;

class Domain extends Model
{
	use HasDateTimeFormatter;
    use SoftDeletes;


    protected static function boot()
    {
        parent::boot();

        $client = new Client();

        static::saved(function ($domain) use ($client) {
            $client->set(\App\Admin\Repositories\Domain::CACHE_KEY . $domain->id, json_encode($domain));
        });

        static::deleted(function ($domain) use ($client) {
            $client->del(\App\Admin\Repositories\Domain::CACHE_KEY . $domain->id);
        });
    }
}
