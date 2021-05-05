<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Predis\Client;

class Member extends Model
{
	use HasDateTimeFormatter;
    use SoftDeletes;

    const MEMBER_CACHE_KEY = 'member_';

    protected static function boot()
    {
        parent::boot();

        $client = new Client();

        static::saved(function ($member) use ($client) {
            $client->set(self::MEMBER_CACHE_KEY . $member->id, json_encode($member));
        });

        static::deleted(function ($member) use ($client) {
            $client->del(self::MEMBER_CACHE_KEY . $member->id);
        });
    }

    public function getMemberByID($id)
    {
        $client = new Client();
        $exist = $client->exists(self::MEMBER_CACHE_KEY . $id);
        if (!$exist) {
            $member = self::where('id', $id)->first();
            if (!$member) {
                return null;
            }
            $client->set(self::MEMBER_CACHE_KEY . $id, json_encode($member));
            return $member;
        }
        $member = $client->get(self::MEMBER_CACHE_KEY . $id);
        return json_decode($member, true);
    }
}
