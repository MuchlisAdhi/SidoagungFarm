<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductRequest;
use App\Models\Product;
use App\Models\Media;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function list()
    {
        return view("admin.product", [
            'products' => Product::all()
        ]);
    }

    public function get($id)
    {
        $id = decrypt($id);
        return response()->json(Product::where("id", $id)->first());
    }

    public function save(SaveProductRequest $request)
    {
        $validated = $request->validated();
        $image = $request->file("image");

        $id = $validated['id'] ?? null;
        if($id)
            $id = decrypt($id);

        $form = [
            'title' => $validated['title'],
            'category' => $validated['category'] ?? '',
            'publish'   => (int) ($validated['publish'] ?? 0),
            'description' => $validated['description'],
        ];

        $imageId = "";
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
            }
        }

        if($imageId)
            $form["mediaId"] = $imageId;

        if($id)
        {
            Product::where("id", $id)->update($form);
        }else{
            Product::create($form);
        }
        
        session()->flash("success", "Berhasil Menyimpan Produk.");

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function publish($id)
    {
        $id = decrypt($id);
        Product::where("id", $id)->update([
            'publish'   => request()->query("publish") == "1" ? 1 : 0
        ]);

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function remove($id)
    {
        $id = decrypt($id);
        $pakan = Product::where('id', $id)->first();

        if(!$pakan)
        {
            session()->flash("error", "Produk tidak ditemukan.");
        }else{
            $pakan->delete();
            session()->flash("success", "Produk berhasil di hapus.");
        }
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
