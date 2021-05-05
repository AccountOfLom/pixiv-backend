<?php

namespace App\Admin\Repositories;

use App\Models\Member as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Member extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
