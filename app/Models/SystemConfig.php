<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemConfig extends Model
{
	use HasDateTimeFormatter;

    protected $table = 'system_configs';

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($config) {
            Cache::put($config->key, $config->value);
        });

        static::deleted(function ($config) {
            Cache::forget($config->key);
        });

    }

}
