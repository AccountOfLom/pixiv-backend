<?php

namespace App\Admin\Repositories;

use App\Models\Follow as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Follow extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
