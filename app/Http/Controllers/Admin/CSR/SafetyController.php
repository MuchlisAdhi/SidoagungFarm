<?php

namespace App\Http\Controllers\Admin\CSR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCsrRequest;
use App\Models\CSR;
use App\Models\Media;
use App\Services\ImageOptimizationService;
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

    public function save(SaveCsrRequest $request)
    {
        $validated = $request->validated();
        $id = null;
        $mode = $this->mode;
        if(session()->has("safetyKey"))
            $id = session()->get("safetyKey");

        $form = [
            'title'  => $validated['formTitle'],
            'mode' => $mode,
            'releasedate'  => $validated['formPostedOn'],
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
