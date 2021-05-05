<?php

namespace App\Admin\Repositories;

use App\Models\Bookmark as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Bookmark extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
