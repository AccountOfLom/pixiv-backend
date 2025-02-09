<?php

namespace App\Admin\Repositories;

use App\Models\Domain as Model;
use Dcat\Admin\Repositories\EloquentRepository;
use phpDocumentor\Reflection\Types\Self_;
use Predis\Client;

class Domain extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    //缓存key
    const CACHE_KEY = 'domain_';

    public static function info($domain) {
        $redis = new Client();
        $conf = $redis->get(self::CACHE_KEY . $domain);
        if ($conf) {
            return json_decode($conf, true);
        }
        $conf = \App\Models\Domain::where('domain', $domain)->first();
        if (!$conf) {
            return null;
        }
        $redis->set(self::CACHE_KEY . $domain, json_encode($conf));
        return $conf->toArray();
    }

}
