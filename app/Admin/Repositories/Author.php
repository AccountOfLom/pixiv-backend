<?php

namespace App\Admin\Repositories;

use App\Models\Author as Model;
use Dcat\Admin\Repositories\EloquentRepository;

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
}
