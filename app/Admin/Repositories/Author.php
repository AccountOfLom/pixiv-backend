<?php

namespace App\Admin\Repositories;

use App\Models\Author as Model;
use Dcat\Admin\Repositories\EloquentRepository;
use Predis\Client;

class Author extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    //已采集信息
    const COLLECTED = 1;

    //未采集信息
    const NO_COLLECT = 0;

    const AUTHOR_CACHE_KEY = 'author_cache_key';


    public function getAuthorByPixivID($pixiv) {
        $client = new Client();
        $exist = $client->hexists(self::AUTHOR_CACHE_KEY, $pixiv);
        if (!$exist) {
            $author = \App\Models\Author::where('pixiv_id', $pixiv)->first();
            if (!$author) {
                return null;
            }
            $client->hset(self::AUTHOR_CACHE_KEY, $pixiv, json_encode($author));
            return $author;
        }
        $author = $client->hget(self::AUTHOR_CACHE_KEY, $pixiv);
        return json_decode($author, true);
    }

}
