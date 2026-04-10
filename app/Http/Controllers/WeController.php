<?php

namespace App\Http\Controllers;

use App\Enums\QuestionType;
use App\Http\Requests\Web\CareerApplyRequest;
use App\Http\Requests\Web\JoinAsPartnerRequest;
use App\Http\Requests\Web\SubmitQuestionRequest;
use App\Models\{
    Career
};
use App\Services\CareerApplicationService;
use App\Services\PartnerApplicationService;
use App\Services\WebsiteInquiryService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use RuntimeException;

class WeController extends Controller
{
    public function __construct(
        protected WebsiteInquiryService $websiteInquiryService,
        protected PartnerApplicationService $partnerApplicationService,
        protected CareerApplicationService $careerApplicationService
    ) {}

    public function summary() {
        return view("we.summary", [
            'questionTypes' => QuestionType::values(),
        ]);
    }

    public function question(SubmitQuestionRequest $request)
    {
        $question = $this->websiteInquiryService->submitGeneralQuestion($request->toPayload());
        $ticketNo = (string) ($question->ticket_no ?: '-');

        return back()->with(
            "success",
            "Pertanyaan telah kami terima. Terima kasih. Nomor tiket Anda: {$ticketNo}"
        );
    }

    public function joinAsPartner(JoinAsPartnerRequest $request)
    {
        $this->partnerApplicationService->submit($request->toPayload());

        return back()->with("success", "Pengajuan telah kami terima. Terima Kasih.");
    }

    public function joinUs() {
        return view("we.join", []);
    }

    public function beOurPartner() {
        return redirect()->route("we.join-us", [], 301);
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

    public function saveApply(CareerApplyRequest $request)
    {
        try {
            $this->careerApplicationService->submit(
                payload: $request->toPayload(),
                experienceRows: $request->experienceRows(),
                cv: $request->cvFile()
            );
        } catch (RuntimeException $th) {
            return back()->withErrors([
                'formCV' => 'CV gagal diunggah. Silakan coba kembali.',
            ])->withInput();
        }

        return back()->with("success", "Anda Berhasil Melamar Pekerjaan Ini.");
    }
}
