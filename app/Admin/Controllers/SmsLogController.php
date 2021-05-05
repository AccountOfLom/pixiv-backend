<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\SmsLog;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class SmsLogController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SmsLog(), function (Grid $grid) {
            $grid->column('id')->sortable();
//            $grid->column('channel_code', '短信渠道代码');
            $grid->column('phone_number', '手机号');
            $grid->column('status', '发送状态');
            $grid->column('order_id', '订单号');
            $grid->column('created_at', '发送时间');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('phone_number', '手机号')->width(3);
                $filter->equal('order_id', '订单号')->width(3);
                $filter->between('created_at', '发送时间')->datetime()->width(3);
                $filter->equal('status', '发送状态')->select(['0' => '发送中', '1' => '已发送', '2' => '发送失败'])->width(3);
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
        return Show::make($id, new SmsLog(), function (Show $show) {
            $show->field('id');
//            $show->field('channel_code');
            $show->field('phone_number');
            $show->field('status');
            $show->field('order_id');
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
        return Form::make(new SmsLog(), function (Form $form) {
            $form->display('id');
//            $form->text('channel_code');
            $form->text('phone_number');
            $form->text('status');
            $form->text('order_id');
            $form->text('created_at');
        });
    }
}
