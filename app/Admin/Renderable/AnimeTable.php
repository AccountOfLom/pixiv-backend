<?php


namespace App\Admin\Renderable;


use App\Models\AnimeGroup;
use Dcat\Admin\Grid\LazyRenderable;
use Dcat\Admin\Grid;

class AnimeTable extends LazyRenderable
{
    public function grid(): Grid
    {
        // 获取外部传递的参数
//        $id = $this->id;

        return Grid::make(new AnimeGroup(), function (Grid $grid) {

            $grid->disableFilterButton();

            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name', '系列名');

            $grid->paginate(10);
            $grid->disableActions();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->like('name', '系列名')->width(4);
            });
        });
    }
}