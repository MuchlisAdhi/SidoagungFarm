<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveHomeBannerMenuRequest;
use App\Http\Requests\Admin\SaveHomeBannerRequest;
use Illuminate\Support\Str;
use App\Models\Banner;
use App\Models\Media;
use App\Services\ImageOptimizationService;

class HomeController extends Controller
{
    public function bannerList()
    {
        return view("admin.home.banner", [
            'banners' => Banner::where("mode", "home")->get()
        ]);
    }

    public function bannerSave(SaveHomeBannerRequest $request)
    {
        $validated = $request->validated();
        $image = $request->file("image");

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

            Banner::create([
                'mode'  => "home",
                'mediaId'   => $imageId,
                'title' => $validated['title'],
                'publish'   => (int) ($validated['publish'] ?? 0),
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

    public function bannerMenuSave(SaveHomeBannerMenuRequest $request)
    {
        $validated = $request->validated();
        $image = $request->file("image");

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

            Banner::create([
                'mode'  => $validated['mode'],
                'mediaId'   => $imageId,
                'title' => $validated['title'],
                'publish'   => (int) ($validated['publish'] ?? 0),
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
