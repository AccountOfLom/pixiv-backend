<?php

namespace App\Admin\Repositories;

use App\Models\Anime as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Anime extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;

    const TYPE_ANIME = 'anime';
}
