<?php

namespace App\Admin\Repositories;

use App\Models\AnimeGroup as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class AnimeGroup extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
