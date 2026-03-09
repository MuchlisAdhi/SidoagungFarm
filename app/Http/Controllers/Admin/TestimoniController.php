<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Testimoni;
use App\Models\Media;

class TestimoniController extends Controller
{
    public function list()
    {
        return view("admin.testimoni", [
            'list' => Testimoni::all()
        ]);
    }

    public function save()
    {
        $name = request()->input("formName");
        $title = request()->input("formTitle");
        $testimoni = request()->input("formTestimoni");
        $image = request()->file("image");

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

            Testimoni::create([
                'photo'   => $imageId,
                'name' => $name,
                'title' => $title,
                'testimoni'   => $testimoni
            ]);

            session()->flash("success", "Berhasil Menyimpan Testimoni.");

            return response()->json([
                'code'  => 200,
                'msg'   => "",
                'data'  => []
            ]);
        }
        
        return response()->json([
            'code'  => 0,
            'msg'   => "Gagal menyimpan Form",
            'data'  => []
        ]);
    }

    public function remove($id)
    {
        $id = decrypt($id);
        $banner = Testimoni::where('id', $id)->first();

        if(!$banner)
        {
            session()->flash("error", "Testimoni tidak ditemukan.");
        }else{
            $banner->delete();
            session()->flash("success", "Testimoni berhasil di hapus.");
        }
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
