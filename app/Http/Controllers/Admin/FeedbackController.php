<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FaqGetRequest;
use App\Http\Requests\Admin\RejectApplicantRequest;
use App\Http\Requests\Admin\ReplyFaqRequest;
use App\Exports\CareerApplyExport;
use App\Services\Admin\CareerFeedbackService;
use App\Services\Admin\FaqFeedbackService;
use App\Services\Admin\PartnerFeedbackService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;

class FeedbackController extends Controller
{
    public function __construct(
        protected CareerFeedbackService $careerFeedbackService,
        protected FaqFeedbackService $faqFeedbackService,
        protected PartnerFeedbackService $partnerFeedbackService
    ) {}

    public function careerList()
    {
        $list = $this->careerFeedbackService->getCareerListWithApplicantCount();

        return view('admin.feedback.career', compact('list'));
    }

    public function getApplicants($id)
    {
        $result = $this->careerFeedbackService->getApplicantsByEncryptedCareerId((string) $id);
        if (! $result) {
            return redirect('/wongelek/feedback/karir')->with('error', 'Career position not found.');
        }

        return view('admin.feedback.career-applicants', [
            'career' => $result['career'],
            'list' => $result['applicants'],
        ]);
    }

    public function getApplicant($id)
    {
        $find = $this->careerFeedbackService->findApplicantByEncryptedId((string) $id);

        $code = $find ? 200 : 0;

        return response()->json([
            'code' => $code,
            'msg' => '',
            'data' => $find,
        ]);
    }

    public function approveApp($id)
    {
        $this->careerFeedbackService->approveApplicant((string) $id);

        return response()->json([
            'code' => 200,
            'msg' => '',
            'data' => [],
        ]);
    }

    public function rejectApp(RejectApplicantRequest $request)
    {
        $validated = $request->validated();

        $updated = $this->careerFeedbackService->rejectApplicant(
            encryptedApplicantId: (string) $validated['id'],
            reason: (string) $validated['reason']
        );

        return response()->json([
            'code' => $updated ? 200 : 0,
            'msg' => $updated ? '' : 'Data tidak ditemukan.',
            'data' => [],
        ]);
    }

    public function exportApplicants($careerId)
    {
        $career = $this->careerFeedbackService->findCareerByEncryptedId((string) $careerId);
        
        if (!$career) {
            return redirect()->back()->with('error', 'Career position not found.');
        }

        $careerId = $career->id;
        $fileName = 'Applicants_' . str_replace(' ', '_', $career->position) . '_' . date('YmdHis') . '.xlsx';
        
        return Excel::download(new CareerApplyExport($careerId), $fileName);
    }

    public function downloadCV($id)
    {
        $resolved = $this->careerFeedbackService->resolveCvDownload((string) $id);

        if (($resolved['code'] ?? 0) !== 200) {
            return response()->json([
                'code' => 404,
                'msg' => (string) ($resolved['msg'] ?? 'CV not found'),
            ], 404);
        }

        if (($resolved['type'] ?? '') === 'local') {
            return response()->download(
                (string) $resolved['path'],
                (string) ($resolved['filename'] ?? 'cv.pdf')
            );
        }

        return Storage::download(
            (string) $resolved['path'],
            (string) ($resolved['filename'] ?? 'cv.pdf')
        );
    }

    public function faqList()
    {
        $list = $this->faqFeedbackService->getFaqList();

        return view('admin.feedback.faq', ['list' => $list]);
    }

    public function faqGet(FaqGetRequest $request)
    {
        $validated = $request->validated();

        try {
            $find = $this->faqFeedbackService->getFaqDetail(
                encryptedId: (string) $validated['id'],
                mode: (string) $validated['mode']
            );
        } catch (InvalidArgumentException $th) {
            return response()->json([
                'code' => 422,
                'msg' => 'Request tidak valid.',
            ], 422);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'code' => 404,
                'msg' => 'Data tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'msg' => '',
            'data' => $find,
        ]);
    }

    public function faqReplied(ReplyFaqRequest $request)
    {
        $validated = $request->validated();
        try {
            $warning = $this->faqFeedbackService->reply(
                encryptedId: (string) $validated['id'],
                mode: (string) $validated['mode'],
                response: trim((string) $validated['response'])
            );
        } catch (InvalidArgumentException $th) {
            return response()->json([
                'code' => 422,
                'msg' => 'Request tidak valid.',
            ], 422);
        } catch (ModelNotFoundException $th) {
            return response()->json([
                'code' => 404,
                'msg' => 'Data tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'msg' => "Jawaban berhasil dikirim." . $warning,
            'data' => [],
        ]);
    }

    public function mitraList()
    {
        $faqs = $this->partnerFeedbackService->list();

        return view("admin.feedback.mitra", ['list' => $faqs]);
    }

    public function mitraGet($id)
    {
        $mitra = $this->partnerFeedbackService->findByEncryptedId((string) $id);
        if (! $mitra) {
            return response()->json([
                'code' => 404,
                'msg' => 'Data tidak ditemukan.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => $mitra
        ]);
    }

    public function mitraReplied($id)
    {
        $this->partnerFeedbackService->markAsReplied((string) $id);

        return response()->json([]);
    }
}
