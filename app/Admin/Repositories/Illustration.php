<?php

namespace App\Admin\Repositories;

use App\Models\Illustration as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Illustration extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    //插画类型
    const TYPE_ILLUST = 'illust';

    //动画类型
    const TYPE_UGOIRA = 'ugoira';
}
