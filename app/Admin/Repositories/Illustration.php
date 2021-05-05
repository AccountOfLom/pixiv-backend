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

    //漫画
    const TYPE_MANGA = 'manga';

    public $typeText = [
        'illust' => '插画',
        'ugoira' => '动画',
        'manga' => '漫画'
    ];


}
