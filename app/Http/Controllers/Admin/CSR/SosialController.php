<?php

namespace App\Http\Controllers\Admin\CSR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CSR;
use App\Models\Media;
use Illuminate\Support\Str;

class SosialController extends Controller
{
    private $mode = "sosial";

    public function list()
    {
        return view("admin.csr.sosial-list", [
            'list'  => CSR::where('mode', $this->mode)->get()
        ]);
    }

    public function add()
    {
        session()->forget('sosialKey');
        return redirect()->to('/wongelek/csr/sosial/form');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
            $find = CSR::where('id', $id)->first();
            if($find)
                session()->put('sosialKey', $id);
        } catch (\Exception $e) {
            session()->forget('sosialKey');
        }finally {
            return redirect()->to('/wongelek/csr/sosial/form');
        }
    }

    public function form()
    {
        $find = new CSR();
        if($id = session()->get("sosialKey"))
            $find = CSR::where('id', $id)->first();

        return view("admin.csr.sosial-form", ['rs' => $find]);
    }

    public function save()
    {
        $id = null;
        $mode = $this->mode;
        if(session()->has("sosialKey"))
            $id = session()->get("sosialKey");

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
                session()->put("sosialKey", $save->id);
            }

            session()->flash("success", "Berhasil Menyimpan Sosial.");

            return redirect("/wongelek/csr/sosial/form");
        } catch (\Exception $th) {
            session()->flash("error", "Gagal Menyimpan Sosial.");
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            CSR::where("id", $id)->delete();
            return redirect("/wongelek/csr/sosial")->with("success", "Sosial berhasil di hapus.");
        } catch (\Exception $th) {
            return redirect("/wongelek/csr/sosial")->with("error", "Gagal menghapus Sosial.");            
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
