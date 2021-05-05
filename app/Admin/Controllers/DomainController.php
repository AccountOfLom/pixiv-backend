<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Domain;
use App\Models\Site;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class DomainController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('域名')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Domain(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('site_id', '站点')->display(function ($value) {
                return Site::where('id', $value)->value('name');
            });
            $grid->column('domain', '域名');
            $grid->column('status', '启用')->switch();
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
        return Show::make($id, new Domain(), function (Show $show) {
            $show->field('id');
            $show->field('site_id');
            $show->field('domain');
            $show->field('status');
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
        return Form::make(new Domain(), function (Form $form) {
            $form->display('id');

            $directors = Site::where('status', '1')->pluck('name', 'id');
            $form->select('site_id', '站点')->options($directors);

            $form->text('domain', '域名');
            $form->switch('status', '启用');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
