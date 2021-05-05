<?php

namespace App\Admin\Repositories;

use App\Models\SmsLog as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SmsLog extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
