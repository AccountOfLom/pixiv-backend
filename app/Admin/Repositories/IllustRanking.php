<?php

namespace App\Admin\Repositories;

use App\Models\IllustRanking as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class IllustRanking extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
