<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;

class IllustRanking extends Model
{
	use HasDateTimeFormatter;
    use SoftDeletes;
    use QueryCacheable;

    public function illustration()
    {
        return $this->hasOne('App\Models\Illustration', 'pixiv_id', 'pixiv_id');
    }
}
