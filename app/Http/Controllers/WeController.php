<?php

namespace App\Http\Controllers;

use App\Jobs\TicketingJob;
use App\Models\{
    Career,
    CareerApply,
    Media,
    ClientQuestion2,
    JoinAsPartner
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Enums\TicketStatus;

class WeController extends Controller
{
    public function summary() {
        return view("we.summary", []);
    }

    public function question()
    {
        request()->validate([
            'formName' => ['required', 'string', 'max:255'],
            'formEmail' => ['required', 'email', 'max:255'],
            'formPhone' => ['required', 'string', 'max:30'],
            'formType' => ['required', 'in:Produk,Kemitraan,Karir'],
            'formDescription' => ['required', 'string', 'max:2000'],
        ]);

        $form = [
            'name'  => request()->input("formName"),
            'email'  => request()->input("formEmail"),
            'phone'  => request()->input("formPhone"),
            'qtype'  => request()->input("formType"),
            'description'  => request()->input("formDescription"),
            'ticket_status' => TicketStatus::New->value,
        ];

        $question = ClientQuestion2::create($form);

        TicketingJob::dispatchSync('q2', (string) $question->id);
        $question->refresh();
        $ticketNo = (string) ($question->ticket_no ?: '-');

        return back()->with(
            "success",
            "Pertanyaan telah kami terima. Terima kasih. Nomor tiket Anda: {$ticketNo}"
        );
    }

    public function joinAsPartner()
    {
        $form = [
            'firstname'  => request()->input("formFirstName"),
            'lastname'  => request()->input("formLastName"),
            'bod'  => request()->input("formBod"),
            'phone'  => request()->input("formPhone"),
            'email'  => request()->input("formEmail"),
            'category'  => request()->input("formCategory"),
            'companyname'  => request()->input("formCompanyName"),
            'companylocation'  => request()->input("formCompanyLocation"),
            'companydescription'  => request()->input("formCompanyDescription"),
        ];

        JoinAsPartner::create($form);

        return back()->with("success", "Pengajuan telah kami terima. Terima Kasih.");
    }

    public function joinUs() {
        return view("we.join", []);
    }

    public function beOurPartner() {
        return view("we.partner", []);
    }

    public function career($id = "") { 
        if($id)
        {
            try {
                $id = decrypt($id);
                $find = Career::where("id", $id)->first();
                return view("we.career-detail", ['rs' => $find]);
            } catch (\Exception $th) {
                
            }
        } 

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

        $rows = Career::where("publish", 1)->select(DB::raw("id, position, location"))->get(); 
        
        // dd($rows);
        $page = request()->query("page", 1);

        $chunk = collect($rows)->chunk(10);
        return view(
            "we.career", 
            [
                'list'  => $chunk[$page - 1] ?? [],
                'total' => count($chunk),
                'current'   => $page
            ]);
    }

    public function apply($id) {
        try {
            $id = decrypt($id);
            $find = Career::where("id", $id)->first();
            return view("we.apply", ['rs' => $find]);
        } catch (\Exception $th) {
           return redirect("/talk-us/career");
        }
    }

    public function saveApply()
    {
        // dd(request()->all());
        // dd(request()->input("companyName1"));
        $careerId = decrypt(request()->input("formCareerId"));
        $form = [
            'careerid'  => $careerId,
            'firstname'  => request()->input("formFirstName"),
            'lastname'  => request()->input("formLastName"),
            'email'  => request()->input("formEmail"),
            'phone'  => request()->input("formPhone"),
            'bod'  => request()->input("formBod"),
            'lasteducation'  => request()->input("formLastEducation"),
            'major'  => request()->input("formMajor"),
            'isexperience'  => request()->input("formIsExperience") == "1" ? true : false,
            'currentsalary'  => request()->input("formCurrentSalary"),
            'expectationsalary'  => request()->input("formExpectSalary"),
            'experiencelist'  => "[]"
        ];

        $expList = [];
        for($i = 1; $i <= (int)request()->input("totalRow"); $i++)
        {
            $expList[] = [
                'companyName'   => request()->input("companyName" . $i),
                'industri'   => request()->input("industri" . $i),
                'position'   => request()->input("position" . $i),
                'lengthOfWork'   => request()->input("lengthOfWork" . $i)
            ];
        }

        $cv = request()->file("formCV");

        $cvId = Str::uuid();
        $ext = strtolower($cv->getClientOriginalExtension());

        if($cv->move(app_path("Uploads"), $path = $cvId . "." . $ext))
        {
            Media::create([
                'mediaId' => $cvId,
                'mediaType' => $_FILES['formCV']['type'],
                'mediaExt' => $ext,
                'resultPath' => $path
            ]);

            $form["cvid"] = $cvId;
            $form["experiencelist"] = json_encode($expList);

            CareerApply::create($form);

            return back()->with("success", "Anda Berhasil Melamar Pekerjaan Ini.");
        }
    }
}
