<?php

namespace App\Admin\Repositories;

use App\Models\Paint as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Paint extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
