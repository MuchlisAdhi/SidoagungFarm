<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Resep;
use App\Models\Media;
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

    public function save()
    {
        $id = null;
        $mode = $this->mode;
        if(session()->has("resepKey"))
            $id = session()->get("resepKey");

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
