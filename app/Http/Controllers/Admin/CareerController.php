<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCareerRequest;

use App\Models\Career;
use Carbon\Carbon;

class CareerController extends Controller
{
    public function list()
    {
        $finds = Career::all();
        foreach($finds as $f)
        {
            $closingdate = Carbon::parse($f->closingdate)->timestamp;
            $now = now()->timestamp;
            if($now > $closingdate)
            {
                $f->update([
                    'publish'   => 0
                ]);
            }
        }
        $list = Career::all();
        return view("admin.career.list", compact("list"));
    }

    public function add()
    {
        session()->forget('careerKey');
        return redirect()->to('/admin/karir/form');
    }

    public function edit($id)
    {
        try {
            $id = decrypt($id);
            $find = Career::where('id', $id)->first();
            if($find)
                session()->put('careerKey', $id);
        } catch (\Exception $e) {
            session()->forget('careerKey');
        }finally {
            return redirect()->to('/admin/karir/form');
        }
    }

    public function form()
    {
        $find = new Career();
        if ($id = session()->get("careerKey")) {
            $find = Career::where('id', $id)->first() ?? new Career();
        }

        return view("admin.career.form", ['rs' => $find]);
    }

    public function save(SaveCareerRequest $request)
    {
        $validated = $request->validated();
        $id = null;
        if(session()->has("careerKey"))
            $id = session()->get("careerKey");

        $form = [
            'position'  => $validated['formPosition'],
            'location'  => $validated['formLocation'],
            'description'  => $validated['formDescription'] ?? null,
            'qualification'  => $validated['formQualification'] ?? null,
            'postedon'  => $validated['formPostedOn'],
            'closingdate'  => $validated['formClosingDate'],
            'publish'  => ($validated['formPublish'] ?? null) == "on" ? 1 : 0,
        ];

        try {
            if($id)
            {
                Career::where('id', $id)->update($form);
            }else{
                $save = Career::create($form);
                session()->put("careerKey", $save->id);
            }
            session()->flash("success", "Berhasil Menyimpan Lowongan Kerja.");
            return redirect("/admin/karir/form");
        } catch (\Exception $th) {
            session()->flash("error", "Gagal Menyimpan Lowongan Kerja.");
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        try {
            $id = decrypt($id);
            Career::where("id", $id)->delete();
            return redirect("/admin/karir")->with("success", "Lowongan Kerja berhasil di hapus.");
        } catch (\Exception $th) {
            return redirect("/admin/karir")->with("error", "Gagal menghapus Lowongan Kerja.");            
        }
    }
}
