<?php

namespace App\Admin\Repositories;

use App\Models\Site as Model;
use Dcat\Admin\Repositories\EloquentRepository;
use Predis\Client;

class Site extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    //缓存key
    const CACHE_KEY = 'site_';

    public static function info($id) {
        $redis = new Client();
        $conf = $redis->get(self::CACHE_KEY . $id);
        if ($conf) {
            return json_decode($conf, true);
        }
        $conf = \App\Models\Site::where('id', $id)->get();
        if (!$conf) {
            return null;
        }
        $redis->set(self::CACHE_KEY . $id, json_encode($conf));
        return $conf->toArray();
    }
}
