<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Anime;
use App\Admin\Repositories\SystemConfig;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AnimeController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('动漫')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Anime(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('title', '标题')->limit(20);
            $grid->column('image', '封面')->display(function ($value) {
                return '<img class="img img-thumbnail" data-action="preview-img" src="'. SystemConfig::getS3ResourcesURL($value) .'" style="max-width:200px;max-height:200px;cursor:pointer" />';
            });
            $grid->column('url', '地址')->display(function () {
                return '<video width="200" height="120" poster="'.SystemConfig::getS3ResourcesURL($this->image).'" controls>
                        <source src="'.SystemConfig::getS3ResourcesURL($this->url, Anime::TYPE_ANIME).'" type="video/mp4">
                    </video>';
            });
            $grid->column('total_view', '查看数')->sortable();
            $grid->column('total_bookmarks', '收藏数')->sortable();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->quickSearch('title', '标题')->placeholder('标题搜索');
            $grid->filter(function (Grid\Filter $filter) {
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
        return Show::make($id, new Anime(), function (Show $show) {
            $show->field('id');
            $show->field('title', '标题');
            $show->field('url', '地址');
            $show->field('image', '封面');
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
        return Form::make(new Anime(), function (Form $form) {
            $form->display('id');
            $form->text('title', '标题');
            $form->text('url', '地址');
            $form->text('image', '封面');
            $form->number('total_view', '查看数');
            $form->number('total_bookmarks', '收藏数');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}