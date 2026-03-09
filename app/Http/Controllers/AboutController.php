<?php

namespace App\Http\Controllers;

use App\Models\CSR;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AboutController extends Controller
{
    public function index() {
      $csr = CSR::select(
        'id',
        'title',
        'thumbnail',
        'content',
        DB::raw("'csr' as mode")
      )->where("publish", 1);

      $news = News::select(
            'id',
            'title',
            'thumbnail',
            'content',
            DB::raw("'news' as mode")
          )->where("publish", 1);

      $join = $csr->union($news)->limit(3)->get();
      return view("about", [
        'news'  => $join
      ]);
    }
}
