<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Bookmark;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class BookmarkController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->header('收藏')
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
        return Grid::make(new Bookmark(), function (Grid $grid) {
            $grid->disableCreateButton();
            $grid->disableDeleteButton();
            $grid->disableEditButton();
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('member_id');
            $grid->column('content_id');
            $grid->column('type', )->display(function ($value) {
                if ($value == \App\Models\Bookmark::ILLUST) {
                    return "<span class='label bg-primary'>插画</span>";
                }
                if ($value == \App\Models\Bookmark::ANIME) {
                    return "<span class='label bg-success'>里番</span>";
                }
                if ($value == \App\Models\Bookmark::PAINT) {
                    return "<span class='label bg-success'>精选</span>";
                }
                return "-";
            });
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
        return Show::make($id, new Bookmark(), function (Show $show) {
            $show->field('id');
            $show->field('member_id');
            $show->field('content_id');
            $show->field('type');
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
        return Form::make(new Bookmark(), function (Form $form) {
            $form->display('id');
            $form->text('member_id');
            $form->text('content_id');
            $form->text('type');
            $form->display('created_at');
        });
    }
}
