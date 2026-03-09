<?php

namespace App\Http\Controllers\Admin\CSR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CSR;
use App\Models\Media;
use Illuminate\Support\Str;

class EnvController extends Controller
{
    private $mode = "env";

    public function list()
    {
        return view("admin.csr.env-list", [
            'list'  => CSR::where('mode', $this->mode)->get()
        ]);
    }

    public function add()
    {
        session()->forget('envKey');
        return redirect()->to('/wongelek/csr/env/form');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
            $find = CSR::where('id', $id)->first();
            if($find)
                session()->put('envKey', $id);
        } catch (\Exception $e) {
            session()->forget('envKey');
        }finally {
            return redirect()->to('/wongelek/csr/env/form');
        }
    }

    public function form()
    {
        $find = new CSR();
        if($id = session()->get("envKey"))
            $find = CSR::where('id', $id)->first();

        return view("admin.csr.env-form", ['rs' => $find]);
    }

    public function save()
    {
        $id = null;
        $mode = $this->mode;
        if(session()->has("envKey"))
            $id = session()->get("envKey");

        $form = [
            'title'  => request()->input("formTitle"),
            'mode' => $mode,
            'releasedate'  => request()->input("formPostedOn"),
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
                CSR::where('id', $id)->update($form);
            }else{
                $save = CSR::create($form);
                session()->put("envKey", $save->id);
            }

            session()->flash("success", "Berhasil Menyimpan Pendidikan.");

            return redirect("/wongelek/csr/env/form");
        } catch (\Exception $th) {
            session()->flash("error", "Gagal Menyimpan Pendidikan.");
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            CSR::where("id", $id)->delete();
            return redirect("/wongelek/csr/env")->with("success", "Pendidikan berhasil di hapus.");
        } catch (\Exception $th) {
            return redirect("/wongelek/csr/env")->with("error", "Gagal menghapus Pendidikan.");            
        }
    }

    public function publish($id)
    {
        $id = decrypt($id);
        $find = CSR::where("id", $id)->first();
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
