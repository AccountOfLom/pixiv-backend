<?php


namespace App\Http\Controllers\v1;


use App\Http\Controllers\Controller;
use App\Models\SearchHistory;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class searchController extends Controller
{
    public function tag(Request $request)
    {
        $keyword = $request->input('keyword');
        if (!$keyword) {
            return $this->error('keyword 不能为空');
        }
        $tags = Tag::whereRaw(DB::raw("name like '%{$keyword}%' or translated_name like '{$keyword}'"))->select(['id', 'name', 'translated_name'])->get();

        return $this->success($tags);
    }

    public function hotTag()
    {
        $tags = DB::table('search_historys as s')
            ->leftJoin('tags', 's.tag_id', '=', 'tags.id')
            ->select(['tags.id', 'tags.name', 'tags.translated_name'])
            ->selectRaw("count(s.id) as hot_value")
            ->groupBy('s.tag_id')
            ->orderBy('hot_value', 'desc')
            ->limit(15)
            ->get();

        return $this->success($tags);
    }
}