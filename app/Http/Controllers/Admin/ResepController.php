<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveResepRequest;
use App\Models\Resep;
use App\Models\Media;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Str;

class ResepController extends Controller
{
    private $mode = "resep";

    public function list()
    {
        return view("admin.resep.resep-list", [
            'list'  => Resep::where('mode', $this->mode)->get()
        ]);
    }

    public function add()
    {
        session()->forget('resepKey');
        return redirect()->to('/wongelek/resep/form');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
            $find = Resep::where('id', $id)->first();
            if($find)
                session()->put('resepKey', $id);
        } catch (\Exception $e) {
            session()->forget('resepKey');
        }finally {
            return redirect()->to('/wongelek/resep/form');
        }
    }

    public function form()
    {
        $find = new Resep();
        if($id = session()->get("resepKey"))
            $find = Resep::where('id', $id)->first();

        return view("admin.resep.resep-form", ['rs' => $find]);
    }

    public function save(SaveResepRequest $request)
    {
        $validated = $request->validated();
        $id = null;
        $mode = $this->mode;
        if(session()->has("resepKey"))
            $id = session()->get("resepKey");

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
                    app(ImageOptimizationService::class)->optimize(app_path("Uploads/" . $path), $ext);

                    Media::create([
                        'mediaId' => $imageId,
                        'mediaType' => $mimeType,
                        'mediaExt' => $ext,
                        'resultPath' => $path
                    ]);

                    $form['thumbnail'] = $imageId;
                }
            }

            if($id)
            {
                Resep::where('id', $id)->update($form);
            }else{
                $save = Resep::create($form);
                session()->put("resepKey", $save->id);
            }

            session()->flash("success", "Berhasil Menyimpan Berita.");

            return redirect("/wongelek/resep/form");
        } catch (\Exception $th) {
            session()->flash("error", "Gagal Menyimpan Berita.");
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            Resep::where("id", $id)->delete();
            return redirect("/wongelek/resep")->with("success", "Berita berhasil di hapus.");
        } catch (\Exception $th) {
            return redirect("/wongelek/resep")->with("error", "Gagal menghapus Berita.");            
        }
    }

    public function publish($id)
    {
        $id = decrypt($id);
        $find = Resep::where("id", $id)->first();
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
