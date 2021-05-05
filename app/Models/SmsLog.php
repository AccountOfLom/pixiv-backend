<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'sms_logs';
    
}
