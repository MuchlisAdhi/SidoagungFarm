<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Media;
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

    public function save()
    {
        $title = request()->input("title");
        $category = request()->input("category");
        $publish = request()->input("publish");
        $desc = request()->input("description");
        $image = request()->file("image");

        $id = request()->input("id");
        if($id)
            $id = decrypt($id);

        $form = [
            'title' => $title,
            'category' => $category,
            'publish'   => $publish,
            'description' => $desc
        ];

        $imageId = "";
        //return response()->json($_FILES);
        if($_FILES['image']['size'] ?? 0 > 0) 
        {
            $imageId = Str::uuid();
            $ext = strtolower($image->getClientOriginalExtension());

            if($image->move(app_path("Uploads"), $path = $imageId . "." . $ext))
            {
                Media::create([
                    'mediaId' => $imageId,
                    'mediaType' => $_FILES['image']['type'],
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
        
        return response()->json([
            'code'  => 0,
            'msg'   => "Gagal menyimpan Form",
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
