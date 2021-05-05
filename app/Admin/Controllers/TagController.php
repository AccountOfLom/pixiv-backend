<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Tag;
use App\Models\Illustration;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\DB;

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

            $grid->enableDialogCreate();

            $grid->model()->orderBy("sort_num")->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name', '标签名');
            $grid->column('translated_name', '标签名翻译')->display(function ($value) {
                return $value ?? "-";
            });
            $grid->column('is_recommend', '首页推荐')->switch();
            $grid->column( '作品数')->display(function () {
                return Illustration::whereRaw(DB::raw("FIND_IN_SET({$this->id}, tag_ids)"))->count();
            });
            $grid->column('collected_date', '采集日期')->display(function ($value) {
                return $value ?: "-";
            });
            $grid->column('is_collected', '标签下作品')->display(function ($value) {
                if ($value == 1) {
                    return "<span class='label bg-primary'>已采集</span>";
                }
                if ($value == 2) {
                    return "<span class='label bg-primary'>采集失败</span>";
                }
                return "<code style='color:#4c60a3'>未采集</code>";
            });
            $grid->column('sort_num', '排序')->editable();
            $grid->column('updated_at');
            $grid->column('created_at');
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('id')->width(3);
                $filter->equal('is_recommend', '首页推荐')->select([1 => '是', 0 => '否'])->width(3);
                $filter->like('name', '标签名')->width(3);
                $filter->like('translated_name', '标签名翻译')->width(3);
                $filter->equal('is_collected', '标签下作品已采集？')->select([0 => '未采集', 1 => '已采集'])->width(3);
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
            $form->number('sort_num', '排序');
            $form->switch('is_recommend', '首页推荐？');
            $form->switch('is_collected', '标签下作品已采集？');
        });
    }
}
