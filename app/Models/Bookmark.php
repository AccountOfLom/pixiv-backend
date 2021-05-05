<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;
use Predis\Client;

class Bookmark extends Model
{
    public $timestamps = false;

    //插画
	const ILLUST = 1;

	//里番
	const ANIME = 2;

	//精选
    const  PAINT = 3;

    const CACHE_KEY = 'bookmark_';


    public static function validCache($memberID, $type, $contentID)
    {
        $r = new Client();
        if (!$r->exists(self::CACHE_KEY . $memberID)) {
            $bookmarks = Bookmark::where('member_id', $memberID)->get();
            if (!$bookmarks) {
                return 0;
            }
            foreach ($bookmarks as $v) {
                $r->hset(self::CACHE_KEY . $memberID, $v['type'] . '_' . $v['content_id'], 1);
            }
        }

        return $r->hexists(self::CACHE_KEY . $memberID, $type . '_' . $contentID);
    }
}
