<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveTestimoniRequest;
use Illuminate\Support\Str;
use App\Models\Testimoni;
use App\Models\Media;
use App\Services\ImageOptimizationService;
use App\Services\MediaCleanupService;

class TestimoniController extends Controller
{
    public function list()
    {
        return view("admin.testimoni", [
            'list' => Testimoni::all()
        ]);
    }

    public function save(SaveTestimoniRequest $request)
    {
        $validated = $request->validated();
        $image = $request->file("image");

        $imageId = Str::uuid();
        $ext = strtolower($image->getClientOriginalExtension());
        $mimeType = $image->getClientMimeType() ?: $image->getMimeType();

        if($image->move(app_path("Uploads"), $path = $imageId . "." . $ext))
        {
            $optimized = app(ImageOptimizationService::class)->optimizeForStorage(
                app_path("Uploads/" . $path),
                $ext,
                $mimeType
            );

            Media::create([
                'mediaId' => $imageId,
                'mediaType' => $optimized['mime_type'],
                'mediaExt' => $optimized['ext'],
                'resultPath' => $optimized['relative_path']
            ]);

            Testimoni::create([
                'photo'   => $imageId,
                'name' => $validated['formName'],
                'title' => $validated['formTitle'],
                'testimoni'   => $validated['formTestimoni'],
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
            $oldMediaId = $banner->photo;
            $banner->delete();
            app(MediaCleanupService::class)->cleanupIfUnused($oldMediaId);
            session()->flash("success", "Testimoni berhasil di hapus.");
        }
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
