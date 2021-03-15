<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Illustration;
use App\Admin\Repositories\SystemConfig;
use App\Admin\Repositories\Tag;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Layout\Content;
use Illuminate\Support\Facades\Cache;

class IllustrationController extends AdminController
{

    public function index(Content $content)
    {
        return $content
            ->header('插画')
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
        return Grid::make(new Illustration(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('pixiv_id', 'P站ID');
            $grid->column('author_pixiv_id' ,'作者PixivID');
            $grid->column('square_medium', '缩略图')->image()->width(40);
            $grid->column('medium', '小图')->width(80);
            $grid->column('title', '标题')->limit(20);
            $grid->column('type', '类型')->display(function ($value) {
                if ($value == Illustration::TYPE_ILLUST) {
                    return "<span class='label bg-primary'>插画</span>";
                }
                if ($value == Illustration::TYPE_UGOIRA) {
                    return "<span class='label bg-success'>动画</span>";
                }
                return "-";
            });
            $grid->column('内容审核')->display(function() {
                if (!$this->x_restrict && !$this->sanity_level) {
                    return '-';
                }
                $showSanityLevel = Cache::get(SystemConfig::SHOW_SANITY_LEVEL);
                $sanityColor = "#586cb1";
                if ($this->sanity_level == $showSanityLevel) {
                    $sanityColor = '#FC2';
                }
                $restrictColor = "#586cb1";
                $restrictText = '-';
                if ($this->x_restrict == 1) {
                    $restrictColor = 'red';
                    $restrictText = 'R18';
                }
                return  '净网级别:<span style="color:'.$sanityColor.'">' . $this->sanity_level . "</span><br/>" .
                    '限制级?:&nbsp;&nbsp;<span style="color:'.$restrictColor.'">' . $restrictText . '</span>';
            });
            $grid->column('原图宽高')->display(function () {
                if ($this->width == '') {
                    return '-';
                }
                return $this->width . " x " . $this->height;
            })->limit(20);
            $grid->column('page_count', '插画数');
            $grid->column('author_collected', '作者信息已采集？')->display(function ($value) {
                if ($value == '') {
                    return '-';
                }
                if ($value == 0) {
                    return "<span class='label bg-warning' style='color:#FFF !important;'>否</span>";
                }
                if ($value == 1) {
                    return "<span class='label bg-success'>是</span>";
                }
                return '-';
            });
            $grid->column('image_collected', '图片已采集？')->display(function ($value) {
                if ($value == 0 || $value == '') {
                    return "<span class='label bg-warning' style='color:#FFF !important;'>否</span>";
                }
                if ($value == 1) {
                    return "<span class='label bg-success'>是</span>";
                }
                return '-';
            });
            $grid->column('tag_ids', '标签')->display(function ($value) {
                if ($value == "") {
                    return '-';
                }
                $tagIDs = explode(',', $value);
                $tagNames = "";
                foreach ($tagIDs as $k => $v) {
                    $tag = (new Tag())->getTagByID($v);
                    $tagNames .= '[' . $tag->name . ', ' . $tag->translated_name . '], ';
                }
                return $tagNames;
            })->limit(10);
            $grid->column('create_date', '发布日期')->display(function ($value) {
                if ($value == "") {
                    return "-";
                }
                return date('Y-m-d', strtotime($value));
            });
            $grid->column('展示数据')->display(function () {
                return  '查看: <span style="color:#586cb1">' . $this->total_view . "</span><br/>" .
                    '收藏: <span style="color:#586cb1">' . $this->total_bookmarks . '</span>';
            });
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->equal('pixiv_id', 'P站ID');
                $filter->like('title', '标题');
                $filter->equal('author_pixiv_id', '作者P站ID');
                $filter->equal('type', '作品类型')->select(['illust' => '插画', 'ugoira' => '动画']);
                $filter->equal('x_restrict', '限制级')->select([0 => '否', 1 => '是']);
                $filter->equal('sanity_level', '净网级别');
                $filter->equal('author_collected', '作者信息已采集')->select([0 => '否', 1 => '是', 2 => '采集失败']);
                $filter->equal('image_collected', '图片已采集')->select([0 => '否', 1 => '是', 2 => '采集失败']);
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
        return Show::make($id, new Illustration(), function (Show $show) {
            $show->field('id');
            $show->field('pixiv_id');
            $show->field('title', '标题');
            $show->field('tag_ids');
            $show->field('author_pixiv_id');
            $show->field('author_collected');
            $show->field('caption');
            $show->field('x_restrict');
            $show->field('sanity_level');
            $show->field('width');
            $show->field('height');
            $show->field('page_count');
            $show->field('image_collected');
            $show->field('create_date');
            $show->field('total_view');
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
        return Form::make(new Illustration(), function (Form $form) {
            $form->display('id');
            $form->text('pixiv_id', 'P站ID');
            $form->text('title', '标题');
            $form->text('author_pixiv_id' ,'作者PixivID');
            $form->text('author_collected', '作者信息已采集？');
            $form->text('caption', '简介');
            $form->text('x_restrict', '限制级？');
            $form->text('sanity_level', '净网级别');
            $form->number('width', '原图宽');
            $form->number('height', '原图高');
            $form->number('page_count', '插画数');
            $form->text('image_collected', '图片已采集？');
            $form->text('tag_ids', '标签');
            $form->date('create_date', '发布日期');
            $form->number('total_view', '查看数');
            $form->number('total_bookmarks', '收藏数');
        });
    }
}
