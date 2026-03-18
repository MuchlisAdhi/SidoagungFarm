<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CSR;
use App\Models\News;
use App\Models\Resep;
use Illuminate\Support\Facades\DB;

class CsrController extends Controller
{
    public function summary() {
        return view("csr.summary", [
            'list'  => News::where("mode", "news")->where("publish", 1)->get()
        ]);
    }

    public function news() {
        return view("csr.news", [
            'list'  => News::where("mode", "news")->where("publish", 1)->get()
        ]);
    }

    public function resep() {
        return view("csr.resep", [
            'list'  => Resep::where("mode", "resep")->where("publish", 1)->get()
        ]);
    }

    public function getList()
    {
        $keyword = request()->query("keyword");
        $page = request()->query("page", 1);
        $mode = request()->query("mode");

        $page = $page ? $page : 1;

        $news = News::where("mode", $mode)->where("publish", 1);
        $csr = CSR::select(
                    'id',
                    'title',
                    'thumbnail',
                    'releasedate',
                    'content',
                    'viewer',
                    'mode',
                    'publish',
                    DB::raw("'' as author"),
                    'created_at',
                    'updated_at'
                )->
                whereIn("mode", ["env", "safety", "sosial"])
                ->where("publish", 1);

        $all = $news->union($csr)->get()->chunk(5);

        return view("csr.list", [
            'list'  => count($all) ? $all[$page - 1] : [],
            'total' => count($all),
            'page'  => $page
        ]);

        /*
        if($mode == "news")
        {
            $find = News::where("mode", $mode)->where("publish", 1);

            if($keyword)
            {
                $keys = explode(" ", $keyword);
                $find = $find->where(function($query)use($keys){
                    for($i = 0; $i < count($keys); $i++)
                    {
                        $query = $query->where("title", "like", "%" .$keys[$i]. "%")->orWhere("content", "like", "%" .$keys[$i]. "%");
                    }
                    return $query;
                });
            }
            
            $find = $find->get()->chunk(4);

            return view("csr.list", [
                'list'  => count($find) ? $find[$page - 1] : [],
                'total' => count($find),
                'page'  => $page
            ]);
        }else{
            $allow = ["env", "safety", "sosial"];
            if(!in_array($mode, $allow))
                return view("csr.list", [
                    'list'  => [],
                    'total' => 0,
                    'page'  => 0
                ]);

            $find = CSR::where("mode", $mode)->where("publish", 1);

            if($keyword)
            {
                $keys = explode(" ", $keyword);
                $find = $find->where(function($query)use($keys){
                    for($i = 0; $i < count($keys); $i++)
                    {
                        $query = $query->where("title", "like", "%" .$keys[$i]. "%")->orWhere("content", "like", "%" .$keys[$i]. "%");
                    }
                    return $query;
                });
            }
            
            $find = $find->get()->chunk(9);

            return view("csr.list", [
                'list'  => count($find) ? $find[$page - 1] : [],
                'total' => count($find),
                'page'  => $page
            ]);
        }
        */
    }

    public function getDetail()
    {
        $slug = request()->query("slug", "");
        $mode = request()->query("mode", "sosial");

        if ($slug) {
            $slug = str_replace("-", " ", $slug);
            $find = null;

            if ($mode === "news") {
                $find = News::where("mode", "news")
                    ->where("title", "like", "%" . $slug . "%")
                    ->first();
            } else {
                $find = Csr::where("mode", $mode)
                    ->where("title", "like", "%" . $slug . "%")
                    ->first();
            }

            if (! $find) {
                return response()->noContent();
            }

            $find->increment('viewer');
            $find->refresh();

            return view("csr.detail", ['r' => $find]);
        }

        return response()->noContent();
    }


}
