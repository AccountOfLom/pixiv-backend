<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Tag;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class TagController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('标签')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Tag(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name', '标签名');
            $grid->column('translated_name', '标签名翻译');
            $grid->column('collected_date', '采集日期');
            $grid->column('is_collected', '标签下作品已采集？');
            $grid->column('updated_at');
            $grid->column('created_at');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('name', '标签名');
                $filter->like('translated_name', '标签名翻译');
                $filter->equal('is_collected', '标签下作品已采集？')->select([0 => '未采集', 1 => '已采集']);
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
        return Show::make($id, new Tag(), function (Show $show) {
            $show->field('id');
            $show->field('name', '标签名');
            $show->field('translated_name', '标签名翻译');
            $show->field('collected_date', '采集日期');
            $show->field('is_collected', '标签下作品已采集？');
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
        return Form::make(new Tag(), function (Form $form) {
            $form->display('id');
            $form->text('name', '标签名');
            $form->text('translated_name', '标签名翻译');
            $form->text('collected_date', '采集日期');
            $form->switch('is_collected', '标签下作品已采集？');
        });
    }
}
