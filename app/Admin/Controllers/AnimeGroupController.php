<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\AnimeGroup;
use App\Models\Anime;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AnimeGroupController extends AdminController
{
    public function index(Content $content)
    {
        return $content
            ->header('里番系列')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new AnimeGroup(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name', '系列名');
            $grid->column('里番数')->display(function() {
                return Anime::where('group_id', $this->id)->count();
            });
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->equal('id', 'ID')->width(3);
                $filter->like('name', '系列名')->width(3);
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
        return Show::make($id, new AnimeGroup(), function (Show $show) {
            $show->field('id');
            $show->field('name');
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
        return Form::make(new AnimeGroup(), function (Form $form) {
            $form->display('id');
            $form->text('name');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
