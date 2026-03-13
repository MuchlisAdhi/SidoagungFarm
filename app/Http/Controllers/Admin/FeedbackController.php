<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectApplicantRequest;
use App\Http\Requests\Admin\ReplyFaqRequest;
use App\Jobs\NotificationJob;
use App\Jobs\TicketingJob;
use App\Models\{
    ClientQuestion,
    ClientQuestion2,
    Ticket,
    Product,
    JoinAsPartner,
    Career,
    CareerApply
};
use App\Services\TicketNumberService;
use App\Enums\TicketStatus;
use App\Exports\CareerApplyExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

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

    public function rejectApp(RejectApplicantRequest $request)
    {
        $validated = $request->validated();
        $id = decrypt($validated['id']);
        CareerApply::where('id', $id)->update([
            'isapprove' => 0,
            'rejectreason'  => $validated['reason'],
        ]);
        return response()->json([
            'code' => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }

    public function exportApplicants($careerId)
    {
        $careerId = decrypt($careerId);
        $career = Career::find($careerId);
        
        if (!$career) {
            return redirect()->back()->with('error', 'Career position not found.');
        }

        $fileName = 'Applicants_' . str_replace(' ', '_', $career->position) . '_' . date('YmdHis') . '.xlsx';
        
        return Excel::download(new CareerApplyExport($careerId), $fileName);
    }

    public function downloadCV($id)
    {
        $id = decrypt($id);
        $applicant = CareerApply::find($id);
        
        if (!$applicant || !$applicant->cvid) {
            return response()->json([
                'code' => 404,
                'msg' => 'CV not found',
            ], 404);
        }

        $filePath = 'public/' . $applicant->cvid;
        
        if (!Storage::exists($filePath)) {
            return response()->json([
                'code' => 404,
                'msg' => 'CV file not found in storage',
            ], 404);
        }

        return Storage::download($filePath);
    }

    public function faqList()
    {
        $q1 = ClientQuestion::select(
            DB::raw("clientquestion.id"),
            DB::raw("clientquestion.name"),
            DB::raw("clientquestion.email"),
            DB::raw("clientquestion.phone"),
            DB::raw("clientquestion.ticket_no"),
            DB::raw("clientquestion.replied"),
            DB::raw("clientquestion.ticket_status"),
            DB::raw("clientquestion.description"),
            DB::raw("clientquestion.response_message"),
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
            "ticket_no",
            "replied",
            "ticket_status",
            "description",
            "response_message",
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
            DB::raw("clientquestion.ticket_no"),
            DB::raw("clientquestion.replied"),
            DB::raw("clientquestion.ticket_status"),
            DB::raw("clientquestion.description"),
            DB::raw("clientquestion.response_message"),
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
                "ticket_no",
                "replied",
                "ticket_status",
                "description",
                "response_message",
                DB::raw("'' as title"),
                DB::raw("qtype as category")
            )->where('id', $id)->first();
        }

        if ($find && ($find->ticket_status ?? TicketStatus::New->value) === TicketStatus::New->value) {
            if ($mode === 'q1') {
                ClientQuestion::where('id', $id)->update([
                    'ticket_status' => TicketStatus::Open->value,
                ]);

                Ticket::where('question_mode', 'q1')
                    ->where('question_id', $id)
                    ->update(['status' => TicketStatus::Open->value]);
            }

            if ($mode === 'q2') {
                ClientQuestion2::where('id', $id)->update([
                    'ticket_status' => TicketStatus::Open->value,
                ]);

                Ticket::where('question_mode', 'q2')
                    ->where('question_id', $id)
                    ->update(['status' => TicketStatus::Open->value]);
            }

            $find->ticket_status = TicketStatus::Open->value;
        }

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => $find
        ]);
    }

    public function faqReplied(ReplyFaqRequest $request)
    {
        $validated = $request->validated();
        $mode = $validated['mode'];
        $response = trim((string) $validated['response']);

        try {
            $id = decrypt($validated['id']);
        } catch (\Throwable $th) {
            return response()->json([
                'code' => 422,
                'msg' => 'Request tidak valid.',
            ], 422);
        }

        $questionModel = $mode === 'q1' ? ClientQuestion::class : ClientQuestion2::class;
        $question = $questionModel::where('id', $id)->first();
        if (! $question) {
            return response()->json(['code' => 404, 'msg' => 'Data tidak ditemukan.'], 404);
        }

        TicketingJob::dispatchSync($mode, (string) $question->id);
        $question->refresh();

        $payload = [
            "replied" => 1,
            "response_message" => $response,
            "responded_at" => now(),
            "ticket_status" => TicketStatus::Responded->value,
        ];
        if (! $question->ticket_no) {
            $payload['ticket_no'] = app(TicketNumberService::class)
                ->generateForModel(Ticket::class, 'SAF', 'ticket_number');
        }

        $questionModel::where('id', $question->id)->update($payload);
        $question->refresh();

        $ticket = Ticket::where('question_mode', $mode)
            ->where('question_id', $question->id)
            ->first();

        if (! $ticket) {
            $ticketNo = (string) ($question->ticket_no ?: app(TicketNumberService::class)
                ->generateForModel(Ticket::class, 'SAF', 'ticket_number'));

            $subject = $mode === 'q2'
                ? (string) ($question->qtype ?: 'Website Inquiry')
                : 'Product Inquiry';

            if ($mode === 'q1') {
                $product = Product::where('id', $question->productid)->first();
                if ($product?->title) {
                    $subject = $product->title;
                    if ($product->category) {
                        $subject .= ' (' . $product->category . ')';
                    }
                }
            }

            $ticket = Ticket::create([
                'ticket_number' => $ticketNo,
                'question_mode' => $mode,
                'question_id' => $question->id,
                'subject' => $subject,
                'message' => $question->description ?: '-',
                'requester_name' => $question->name,
                'requester_email' => $question->email,
                'requester_phone' => $question->phone,
                'status' => TicketStatus::Responded->value,
                'priority' => 'normal',
                'channel' => 'website',
                'response_message' => $response,
                'responded_at' => now(),
            ]);
        } else {
            $ticket->update([
                'status' => TicketStatus::Responded->value,
                'response_message' => $response,
                'responded_at' => now(),
            ]);
        }

        $warning = "";
        if ($question && $question->email) {
            try {
                NotificationJob::dispatch(
                    notificationType: 'ticket-responded',
                    questionMode: $mode,
                    questionId: (string) $question->id,
                    ticketId: $ticket?->id
                );
            } catch (\Throwable $th) {
                report($th);
                $warning = " Jawaban tersimpan, tetapi email gagal dikirim.";
            }
        }

        return response()->json([
            'code' => 200,
            'msg' => "Jawaban berhasil dikirim." . $warning,
            'data' => [],
        ]);
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
