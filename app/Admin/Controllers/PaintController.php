<?php

namespace App\Admin\Controllers;

use App\Admin\Renderable\PaintTable;
use App\Admin\Repositories\Paint;
use App\Admin\Repositories\SystemConfig;
use App\Admin\Repositories\Tag;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PaintController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('精选')
            ->body($this->grid());
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Paint(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('原图宽高')->display(function () {
                return $this->width . '*' . $this->height;
            });
            $grid->column('title', '标题')->display(function ($value) {
                return $value ?? '-';
            });
            $grid->column( '图片')->display(function () {
                if (!$this->url) {
                    return '-';
                }
                return '<img class="img img-thumbnail" data-action="preview-img" src="'. SystemConfig::getS3ResourcesURL($this->url) .'" style="max-width:200px;max-height:200px;cursor:pointer" />';
            });
            $grid->column('tag_ids', '标签')->display(function ($value) {
                if ($value == "") {
                    return '-';
                }
                $tagIDs = explode(',', $value);
                $tagNames = "";
                foreach ($tagIDs as $k => $v) {
                    $tag = (new Tag())->getTagByID($v);
                    if (!$tag) {
                        continue;
                    }
                    $tagNames .= '[' . $tag->name . ', ' . $tag->translated_name . '(' . $tag->id .')], ';
                }
                return $tagNames;
            })->limit(15);
            $grid->column('total_view', '查看数')->sortable();
            $grid->column('total_bookmarks', '收藏数')->sortable();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
        
            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
                $filter->like('title', '标题')->width(3);
                $filter->where('search', function ($query) {
                    $query->whereRaw(DB::raw("FIND_IN_SET({$this->input}, tag_ids)"));
                }, '标签ID')->width(3);
                $filter->between('created_at', '创建时间')->datetime()->width(3);
        
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
        return Show::make($id, new Paint(), function (Show $show) {
            $show->field('id');
            $show->field('width');
            $show->field('height');
            $show->field('thumbnail');
            $show->field('url');
            $show->field('title');
            $show->field('tag_ids');
            $show->field('total_view');
            $show->field('total_bookmarks');
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
        return Form::make(new Paint(), function (Form $form) {
            $form->display('id');
            $form->text('title', '标题');

            $form->multipleSelectTable('tag_ids', '标签')
                ->title('标签（可多选）')
                ->from(PaintTable::make());

            $form->multipleImage('url', '图片')->url('image');

            $form->display('created_at');
            $form->display('updated_at');

            $form->saving(function (Form $form) {
                if ($form->url == "") {
                    return $form->response()->warning('请上传图片');
                }

                $images = explode(',', $form->url);

//                $form->url = $images[0];
////                $form->thumbnail = FileController::getThumbnailIngName($images[0]);
////                unset($images[0]);

                foreach ($images as $image) {
                    $paint = \App\Models\Paint::where('url', $image)->first();
                    if (!$paint) {
                        $paint = new \App\Models\Paint();
                    }
                    $paint->title = $form->title;
                    $paint->tag_ids = $form->tag_ids;
                    $paint->url = $image;
                    $paint->thumbnail = FileController::getThumbnailIngName($image);
                    $paint->file_md5 = md5(file_get_contents(SystemConfig::getS3ResourcesURL($image)));
                    $paint->width = Cache::get($image . 'width');
                    $paint->height = Cache::get($image . 'height');
                    $paint->save();

                }
                $msg = '保存成功';
                return $form->response()->success($msg)->refresh();
            });
        });
    }
}
