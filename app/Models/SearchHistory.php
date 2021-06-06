<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class SearchHistory extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'search_historys';
    
}
