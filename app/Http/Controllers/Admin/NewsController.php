<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\Media;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    private $mode = "news";

    public function list()
    {
        return view("admin.news.news-list", [
            'list'  => News::where('mode', $this->mode)->get()
        ]);
    }

    public function add()
    {
        session()->forget('newsKey');
        return redirect()->to('/wongelek/news/form');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
            $find = News::where('id', $id)->first();
            if($find)
                session()->put('newsKey', $id);
        } catch (\Exception $e) {
            session()->forget('newsKey');
        }finally {
            return redirect()->to('/wongelek/news/form');
        }
    }

    public function form()
    {
        $find = new News();
        if($id = session()->get("newsKey"))
            $find = News::where('id', $id)->first();

        return view("admin.news.news-form", ['rs' => $find]);
    }

    public function save()
    {
        $id = null;
        $mode = $this->mode;
        if(session()->has("newsKey"))
            $id = session()->get("newsKey");

        $form = [
            'title'  => request()->input("formTitle"),
            'mode' => $mode,
            'releasedate'  => request()->input("formPostedOn"),
            'author'  => request()->input("formAuthor"),
            'content'  => request()->input("formContent"),
            'publish'  => request()->input("formPublish") == "on" ? 1 : 0,
        ];

        $image = request()->file("formThumbnail");

        $imageId = Str::uuid();
        $ext = strtolower($image->getClientOriginalExtension());

        try {
            if($image->move(app_path("Uploads"), $path = $imageId . "." . $ext))
            {
                Media::create([
                    'mediaId' => $imageId,
                    'mediaType' => $_FILES['formThumbnail']['type'],
                    'mediaExt' => $ext,
                    'resultPath' => $path
                ]);

                $form['thumbnail'] = $imageId;
            }

            if($id)
            {
                News::where('id', $id)->update($form);
            }else{
                $save = News::create($form);
                session()->put("newsKey", $save->id);
            }

            session()->flash("success", "Berhasil Menyimpan Berita.");

            return redirect("/wongelek/news/form");
        } catch (\Exception $th) {
            session()->flash("error", "Gagal Menyimpan Berita.");
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            News::where("id", $id)->delete();
            return redirect("/wongelek/news")->with("success", "Berita berhasil di hapus.");
        } catch (\Exception $th) {
            return redirect("/wongelek/news")->with("error", "Gagal menghapus Berita.");            
        }
    }

    public function publish($id)
    {
        $id = decrypt($id);
        $find = News::where("id", $id)->first();
        if($find)
        {
            $find->update([
                'publish'   => request()->query("publish") == "1" ? 1 : 0
            ]);
        }        

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
