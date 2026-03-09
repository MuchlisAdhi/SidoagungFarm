<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    ClientQuestion,
    ClientQuestion2,
    JoinAsPartner,
    Career,
    CareerApply
};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeedbackController extends Controller
{
    public function careerList()
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
        $list = DB::table("career as cr")->select(
            DB::raw("cr.*"),
            DB::raw("(select count(apl.id) from careerapply as apl where apl.careerid = cr.id) as applicants")
        )->get();
        
        return view("admin.feedback.career", compact("list"));
    }

    public function getApplicants($id)
    {
        $id = decrypt($id);

        $find = Career::where("id", $id)->first();
        $applicants = CareerApply::where("careerid", $find->id)->get();
        return view("admin.feedback.career-applicants", [
            'career'    => $find,
            'list'  => $applicants
        ]);
    }

    public function getApplicant($id)
    {
        $id = decrypt($id);
        $find = CareerApply::where('id', $id)->first();

        $code = 200;
        if(!$find)
            $code = 0;

        return response()->json([
            'code' => $code,
            'msg'   => "",
            'data'  => $code ? $find : null
        ]);
    }

    public function approveApp($id)
    {
        $id = decrypt($id);
        CareerApply::where('id', $id)->update([
            'isapprove' => 1,
            'rejectreason'  => ""
        ]);
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function rejectApp()
    {
        $id = decrypt(request()->input("id"));
        CareerApply::where('id', $id)->update([
            'isapprove' => 0,
            'rejectreason'  => request()->input("reason")
        ]);
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function faqList()
    {
        $q1 = ClientQuestion::select(
            DB::raw("clientquestion.id"),
            DB::raw("clientquestion.name"),
            DB::raw("clientquestion.email"),
            DB::raw("clientquestion.phone"),
            DB::raw("clientquestion.replied"),
            DB::raw("clientquestion.description"),
            DB::raw("clientquestion.created_at"),
            DB::raw("product.title"),
            DB::raw("product.category"),
            DB::raw("'q1' as mode")
        )->leftJoin("product", function($join){
            $join->on("clientquestion.productid", "=", "product.id");
        });

        $q2 = ClientQuestion2::select(
            "id",
            "name",
            "email",
            "phone",
            "replied",
            "description",
            "created_at",
            DB::raw("'' as title"),
            DB::raw("qtype as category"),
            DB::raw("'q2' as mode")
        );

        $list = $q2->union($q1)
            ->orderBy("created_at", "desc")
            ->orderBy("replied", "asc")
            ->get();

        return view("admin.feedback.faq", ['list' => $list]);
    }

    public function faqGet()
    {
        $id = request()->query("id", "");
        $id = $id ? decrypt($id) : "";
        $mode = request()->query("mode", "");

        $find = ClientQuestion::select(
            DB::raw("clientquestion.id"),
            DB::raw("clientquestion.name"),
            DB::raw("clientquestion.email"),
            DB::raw("clientquestion.phone"),
            DB::raw("clientquestion.replied"),
            DB::raw("clientquestion.description"),
            DB::raw("product.title"),
            DB::raw("product.category")
        )->leftJoin("product", function($join){
            $join->on("clientquestion.productid", "=", "product.id");
        })->where('clientquestion.id', $id)->first();

        if($mode == "q2")
        {
            $find = ClientQuestion2::select(
                "id",
                "name",
                "email",
                "phone",
                "replied",
                "description",
                DB::raw("'' as title"),
                DB::raw("qtype as category")
            )->where('id', $id)->first();
        }

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => $find
        ]);
    }

    public function faqReplied()
    {
        $id = request()->query("id", "");
        $mode = request()->query("mode", "");

        $id = $id ? decrypt($id) : "";

        if($mode == 'q1')
            ClientQuestion::where('id', $id)->update([
                "replied" => 1
            ]);

        if($mode == 'q2')
            ClientQuestion2::where('id', $id)->update([
                "replied" => 1
            ]);
        return response()->json([]);
    }

    public function mitraList()
    {
        $faqs = JoinAsPartner::orderBy("created_at", "desc")
            ->orderBy("replied", "asc")
            ->get();
        return view("admin.feedback.mitra", ['list' => $faqs]);
    }

    public function mitraGet($id)
    {
        $id = decrypt($id);
        $mitra = JoinAsPartner::where("id", $id)
            ->firstOrFail();
        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => $mitra
        ]);
    }

    public function mitraReplied($id)
    {
        $id = decrypt($id);
        JoinAsPartner::where('id', $id)->update([
            "replied" => 1
        ]);
        return response()->json([]);
    }
}
