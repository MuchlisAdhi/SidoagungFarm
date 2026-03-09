<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Banner;
use App\Models\Media;

class HomeController extends Controller
{
    public function bannerList()
    {
        return view("admin.home.banner", [
            'banners' => Banner::where("mode", "home")->get()
        ]);
    }

    public function bannerSave()
    {
        $title = request()->input("title");
        $publish = request()->input("publish");
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

            Banner::create([
                'mode'  => "home",
                'mediaId'   => $imageId,
                'title' => $title,
                'publish'   => $publish
            ]);

            session()->flash("success", "Berhasil Menyimpan Banner.");

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

    public function bannerPublish($id)
    {
        $id = decrypt($id);
        Banner::where("id", $id)->update([
            'publish'   => request()->query("publish") == "1" ? 1 : 0
        ]);

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function bannerRemove($id)
    {
        $id = decrypt($id);
        $banner = Banner::where('id', $id)->first();

        if(!$banner)
        {
            session()->flash("error", "Banner tidak ditemukan.");
        }else{
            $banner->delete();
            session()->flash("success", "Banner berhasil di hapus.");
        }
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function bannerMenuList()
    {
        return view("admin.home.banner-menu", [
            'banners' => Banner::where("mode","!=", "home")->get()
        ]);
    }

    public function bannerMenuSave()
    {
        $title = request()->input("title");
        $publish = request()->input("publish");
        $mode = request()->input("mode");
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

            Banner::create([
                'mode'  => $mode,
                'mediaId'   => $imageId,
                'title' => $title,
                'publish'   => $publish
            ]);

            session()->flash("success", "Berhasil Menyimpan Banner.");

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

    public function bannerMenuPublish($id)
    {
        $id = decrypt($id);
        $find = Banner::where("id", $id)->first();
        if($find)
        {
            $mode = $find->mode;
            Banner::where("mode", $mode)->update([
                'publish'   => 0
            ]);

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

    public function bannerMenuRemove($id)
    {
        $id = decrypt($id);
        $banner = Banner::where('id', $id)->first();

        if(!$banner)
        {
            session()->flash("error", "Banner tidak ditemukan.");
        }else{
            $banner->delete();
            session()->flash("success", "Banner berhasil di hapus.");
        }
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
