<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Site;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class SiteController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('站点')
            ->body($this->grid());
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Site(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name', '站点名');
            $grid->column('sanity_level', 'P站净网等级')->display(function ($lv) {
                return 'Lv:'.$lv;
            });
            $grid->column('x_restrict', 'P站R18')->switch();
            $grid->column('anime_view', '动漫')->switch();
            $grid->column('paint_view', '绘画')->switch();
            $grid->column('status', '启用')->switch();
            $grid->column('remarks', '备注');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
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
        return Show::make($id, new Site(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('sanity_level');
            $show->field('x_restrict');
            $show->field('status');
            $show->field('remarks');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Site(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->number('sanity_level', 'P站净网等级');
            $form->switch('x_restrict', 'P站R18');
            $form->switch('anime_view', '动漫');
            $form->switch('paint_view', '绘画');
            $form->switch('status', '启用');
            $form->text('remarks', '备注');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
