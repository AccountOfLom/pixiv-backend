<?php
/**
 * Created by PhpStorm.
 * User: shali
 * Date: 2021/4/10
 * Time: 0:08
 */

namespace App\Admin\Renderable;

use App\Models\Tag;
use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;

class PaintTable extends LazyRenderable
{
    public function grid(): Grid
    {
        // 获取外部传递的参数
//        $id = $this->id;

        return Grid::make(new Tag(), function (Grid $grid) {

            $grid->disableFilterButton();

            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name', '标签名');
            $grid->column('translated_name', '标签名翻译')->display(function ($value) {
                return $value ?? '-';
            });

            $grid->paginate(10);
            $grid->disableActions();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->expand();
                $filter->like('name', '标签名')->width(4);
                $filter->like('translated_name', '标签名翻译')->width(4);
            });
        });
    }
}