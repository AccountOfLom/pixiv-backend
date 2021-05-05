<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Member;
use App\Admin\Repositories\Site;
use App\Admin\Repositories\SystemConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class MemberController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Member(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('avatar')->display(function ($avatar) {
                return SystemConfig::getS3ResourcesURL($avatar);
            })->image('', 70, 70);
//            $grid->column('phone_number');
            $grid->column('email');
//            $grid->column('password_original');
            $grid->column('site_id')->display(function ($value) {
                return Site::info($value)['name'];
            });
            $grid->column('p_id')->display(function ($value) {
                return $value == 0 ? '-' : $value;
            });
            $grid->column('nikename');
            $grid->column('promotion_code');
            $grid->column('vip');
            $grid->column('state', '状态')->switch();
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
        return Show::make($id, new Member(), function (Show $show) {
            $show->field('id');
//            $show->field('phone_number');
            $show->field('email');
            $show->field('password');
            $show->field('password_original');
            $show->field('site_id');
            $show->field('p_id');
            $show->field('nikename');
            $show->field('avatar');
            $show->field('promotion_code');
            $show->field('vip');
            $show->field('state');
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
        return Form::make(new Member(), function (Form $form) {
            $form->display('id');
//            $form->text('phone_number');
            $form->text('email');
            $form->text('password');
            $form->text('password_original');
            $form->text('site_id');
            $form->text('p_id');
            $form->text('nikename');
            $form->text('avatar');
            $form->text('promotion_code');
            $form->text('vip');
            $form->switch('state');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
