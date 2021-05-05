<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\SystemConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;

class SystemConfigController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('设置')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SystemConfig(), function (Grid $grid) {

            $grid->enableDialogCreate();
            $grid->setDialogFormDimensions('700px', '450px');

            $grid->column('id')->sortable();
            $grid->column('remarks', '设置项');
            $grid->column('key');
            $grid->column('value')->limit(30);
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->like('remarks', '设置项')->placeholder("输入关键字")->width(4);
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
        return Show::make($id, new SystemConfig(), function (Show $show) {
            $show->field('id');
            $show->field('remarks');
            $show->field('key');
            $show->field('value');
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
        return Form::make(new SystemConfig(), function (Form $form) {
            $form->display('id');
            $form->text('remarks', '设置项名称');
            $form->text('key');
            $form->text('value');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
