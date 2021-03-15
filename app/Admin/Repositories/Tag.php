<?php

namespace App\Admin\Repositories;

use App\Models\Tag as Model;
use Dcat\Admin\Repositories\EloquentRepository;
use Predis\Client;

class Tag extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    //缓存key
    const TAG_CACHE_KEY = 'tags';


    public function getTagByName($name) {
        $client = new Client();
        $exist = $client->hexists(self::TAG_CACHE_KEY, $name);
        if (!$exist) {
            $tag = \App\Models\Tag::where('name', $name)->first();
            if (!$tag) {
                return null;
            }
            $client->hset(self::TAG_CACHE_KEY, $name, json_encode($tag));
            return $tag;
        }
        $tag = $client->hget(self::TAG_CACHE_KEY, $name);
        return json_decode($tag);
    }


    public function getTagByID($id) {
        $client = new Client();
        $exist = $client->hexists(self::TAG_CACHE_KEY, $id);
        if (!$exist) {
            $tag = \App\Models\Tag::where('id', $id)->first();
            if (!$tag) {
                return null;
            }
            $client->hset(self::TAG_CACHE_KEY, $id, json_encode($tag));
            return $tag;
        }
        $tag = $client->hget(self::TAG_CACHE_KEY, $id);
        return json_decode($tag);
    }
}
