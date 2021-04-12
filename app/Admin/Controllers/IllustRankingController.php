<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\IllustRanking;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class IllustRankingController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('作品排行')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new IllustRanking(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('pixiv_id', 'P站ID');
            $grid->column('mode', '模式')->display(function ($value) {
                if ($value == 'day_r18') {
                    return "R18日排行";
                }
            });
            $grid->column('date', '日期');
            $grid->column('created_at');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new IllustRanking(), function (Show $show) {
            $show->field('id');
            $show->field('pixiv_id');
            $show->field('mode');
            $show->field('date');
            $show->field('updated_at');
            $show->field('created_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new IllustRanking(), function (Form $form) {
            $form->display('id');
            $form->text('pixiv_id');
            $form->text('mode');
            $form->text('date');
            $form->text('updated_at');
            $form->text('created_at');
        });
    }
}
