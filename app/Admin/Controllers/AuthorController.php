<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Author;
use App\Admin\Repositories\SystemConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;

class AuthorController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('作者')
            ->body($this->grid());
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Author(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('profile_image_url')->display(function ($value) {
                if ($value) {
                    return "<img class='round' data-action='preview-img' width='40' height='40' src='".SystemConfig::getS3ResourcesURL($value)."' />";
                }
                return "";
            });
            $grid->column('pixiv_id');
            $grid->column('name');
            $grid->column('account');
            $grid->column('comment')->limit(10);
            $grid->column('total_follow_users');
            $grid->column('is_priority_collect')->switch();
            $grid->column('is_add_manully')->display(function ($value) {
                if ($value == 1) {
                    return "<span class='label bg-primary'>手动添加</span>";
                }
                return "<span class='label bg-success'>关联采集</span>";
            });
            $grid->column('is_collected_illust')->display(function ($value) {
                if ($value == 1) {
                    return "<span class='label bg-primary'>已采集</span>";
                }
                if ($value == 2) {
                    return "<span class='label bg-primary'>采集失败</span>";
                }
                return "<code style='color:#4c60a3'>未采集</code>";
            });
            $grid->column('collected_illust_date')->display(function ($value) {
                if ($value) {
                    return $value;
                }
                return "-";
            });
            $grid->column('collected_date');
            $grid->column('created_at');
            $grid->column('updated_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('pixiv_id');
                $filter->equal('is_collected_illust', '作品采集')->select([0 => '未采集', 1 => '已采集', 2 => '采集失败']);
                $filter->equal('is_add_manully', '来源')->select([0 => '关联采集', 1 => '手动添加']);
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
        return Show::make($id, new Author(), function (Show $show) {
            $show->field('id');
            $show->field('p_profile_image_url');
            $show->field('pixiv_id');
            $show->field('name');
            $show->field('account');
            $show->field('comment');
            $show->field('total_follow_users');
            $show->field('is_priority_collect');
            $show->field('is_add_manully');
            $show->field('collected_illust_date');
            $show->field('is_collected_illust');
            $show->field('p_background_image_url');
            $show->field('collected_date');
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
        return Form::make(new Author(), function (Form $form) {
            $form->display('id');
            $form->text('pixiv_id');
            $form->switch('is_priority_collect' )->default(1);
            $form->text('is_add_manully')->default(1);
            $form->text('p_profile_image_url');
            $form->text('name');
            $form->text('account');
            $form->text('comment');
            $form->text('total_follow_users');
            $form->text('collected_illust_date');
            $form->switch('is_collected_illust')->default(0);
            $form->text('p_background_image_url');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
