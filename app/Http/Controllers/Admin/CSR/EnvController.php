<?php

namespace App\Http\Controllers\Admin\CSR;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCsrRequest;
use App\Models\CSR;
use App\Models\Media;
use App\Services\ImageOptimizationService;
use App\Services\MediaCleanupService;
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

    public function save(SaveCsrRequest $request)
    {
        $validated = $request->validated();
        $id = null;
        $mode = $this->mode;
        if(session()->has("envKey"))
            $id = session()->get("envKey");
        $existingItem = $id ? CSR::where('id', $id)->first() : null;
        $oldMediaId = $existingItem?->thumbnail;

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
                if (! empty($form['thumbnail']) && $oldMediaId && $oldMediaId !== $form['thumbnail']) {
                    app(MediaCleanupService::class)->cleanupIfUnused($oldMediaId);
                }
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
            $item = CSR::where("id", $id)->first();
            if (! $item) {
                return redirect("/wongelek/csr/env")->with("error", "Pendidikan tidak ditemukan.");
            }

            $oldMediaId = $item->thumbnail;
            $item->delete();
            app(MediaCleanupService::class)->cleanupIfUnused($oldMediaId);
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
