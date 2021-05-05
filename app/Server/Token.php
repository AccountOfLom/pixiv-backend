<?php


namespace App\Server;


use App\Models\Member;
use Predis\Client;

class Token
{
    //有效期30天
    const expire = 2592000;

    public static function create($id)
    {
        $str = md5(uniqid() . $id);
        $r = new Client();
        $r->set($str, $id, 'ex', self::expire);
        return $str;
    }

    public static function valid($token)
    {
        $r = new Client();
        if (!$r->exists($token)) {
            return false;
        }

        $id = $r->get($token);
        $r->set($token, $id, 'ex', self::expire);
        return (new Member())->getMemberByID($id);
    }
}