<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveNewsRequest;
use App\Models\News;
use App\Models\Media;
use App\Services\ImageOptimizationService;
use App\Services\MediaCleanupService;
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
        return redirect()->to('/admin/news/form');
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
            return redirect()->to('/admin/news/form');
        }
    }

    public function form()
    {
        $find = new News();
        if($id = session()->get("newsKey"))
            $find = News::where('id', $id)->first();

        return view("admin.news.news-form", ['rs' => $find]);
    }

    public function save(SaveNewsRequest $request)
    {
        $validated = $request->validated();
        $id = null;
        $mode = $this->mode;
        if(session()->has("newsKey"))
            $id = session()->get("newsKey");
        $existingNews = $id ? News::where('id', $id)->first() : null;
        $oldMediaId = $existingNews?->thumbnail;

        $form = [
            'title'  => $validated['formTitle'],
            'mode' => $mode,
            'releasedate'  => $validated['formPostedOn'],
            'author'  => $validated['formAuthor'],
            'content'  => $validated['formContent'] ?? null,
            'publish'  => ($validated['formPublish'] ?? null) == "on" ? 1 : 0,
        ];

        $image = $request->file("formThumbnail");

        try {
            if($image)
            {
                $imageId = Str::uuid();
                $ext = strtolower($image->getClientOriginalExtension());
                $mimeType = $image->getClientMimeType() ?: $image->getMimeType();
                if($image->move(app_path("Uploads"), $path = $imageId . "." . $ext))
                {
                    $optimized = app(ImageOptimizationService::class)->optimizeForStorage(
                        app_path("Uploads/" . $path),
                        $ext,
                        $mimeType
                    );

                    Media::create([
                        'mediaId' => $imageId,
                        'mediaType' => $optimized['mime_type'],
                        'mediaExt' => $optimized['ext'],
                        'resultPath' => $optimized['relative_path']
                    ]);

                    $form['thumbnail'] = $imageId;
                }
            }

            if($id)
            {
                News::where('id', $id)->update($form);
                if (! empty($form['thumbnail']) && $oldMediaId && $oldMediaId !== $form['thumbnail']) {
                    app(MediaCleanupService::class)->cleanupIfUnused($oldMediaId);
                }
            }else{
                $save = News::create($form);
                session()->put("newsKey", $save->id);
            }

            session()->flash("success", "Berhasil Menyimpan Berita.");

            return redirect("/admin/news/form");
        } catch (\Exception $th) {
            session()->flash("error", "Gagal Menyimpan Berita.");
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            $news = News::where("id", $id)->first();
            if (! $news) {
                return redirect("/admin/news")->with("error", "Berita tidak ditemukan.");
            }

            $oldMediaId = $news->thumbnail;
            $news->delete();
            app(MediaCleanupService::class)->cleanupIfUnused($oldMediaId);
            return redirect("/admin/news")->with("success", "Berita berhasil di hapus.");
        } catch (\Exception $th) {
            return redirect("/admin/news")->with("error", "Gagal menghapus Berita.");            
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
