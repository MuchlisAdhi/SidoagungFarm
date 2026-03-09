<?php

namespace App\Http\Controllers\Admin\CSR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CSR;
use App\Models\Media;
use Illuminate\Support\Str;

class SafetyController extends Controller
{
    private $mode = "safety";

    public function list()
    {
        return view("admin.csr.safety-list", [
            'list'  => CSR::where('mode', $this->mode)->get()
        ]);
    }

    public function add()
    {
        session()->forget('safetyKey');
        return redirect()->to('/wongelek/csr/safety/form');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
            $find = CSR::where('id', $id)->first();
            if($find)
                session()->put('safetyKey', $id);
        } catch (\Exception $e) {
            session()->forget('safetyKey');
        }finally {
            return redirect()->to('/wongelek/csr/safety/form');
        }
    }

    public function form()
    {
        $find = new CSR();
        if($id = session()->get("safetyKey"))
            $find = CSR::where('id', $id)->first();

        return view("admin.csr.safety-form", ['rs' => $find]);
    }

    public function save()
    {
        $id = null;
        $mode = $this->mode;
        if(session()->has("safetyKey"))
            $id = session()->get("safetyKey");

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
                session()->put("safetyKey", $save->id);
            }

            session()->flash("success", "Berhasil Menyimpan Keselamatan Kerja.");

            return redirect("/wongelek/csr/safety/form");
        } catch (\Exception $th) {
            session()->flash("error", "Gagal Menyimpan Keselamatan Kerja.");
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            CSR::where("id", $id)->delete();
            return redirect("/wongelek/csr/safety")->with("success", "Keselamatan Kerja berhasil di hapus.");
        } catch (\Exception $th) {
            return redirect("/wongelek/csr/safety")->with("error", "Gagal menghapus Keselamatan Kerja.");            
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
