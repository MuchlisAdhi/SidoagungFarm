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
use App\Services\PdfCompressionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Enums\TicketStatus;

class WeController extends Controller
{
    public function __construct(
        protected PdfCompressionService $pdfCompressionService
    ) {}

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
            'formDescription' => ['required', 'string', 'max:10000'],
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
        request()->validate([
            'formFirstName' => ['required', 'string', 'max:255'],
            'formLastName' => ['required', 'string', 'max:255'],
            'formBod' => ['required', 'date'],
            'formPhone' => ['required', 'string', 'max:30'],
            'formEmail' => ['required', 'email', 'max:255'],
            'formCategory' => ['required', 'in:Kemitraan'],
            'formCompanyName' => ['required', 'string', 'max:255'],
            'formCompanyLocation' => ['required', 'string', 'max:255'],
            'formCompanyDescription' => ['required', 'string', 'max:10000'],
        ]);

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
        return redirect()->route("we.join-us");
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
        request()->validate([
            'formCareerId' => ['required', 'string'],
            'formFirstName' => ['required', 'string', 'max:255'],
            'formLastName' => ['required', 'string', 'max:255'],
            'formEmail' => ['required', 'email', 'max:255'],
            'formPhone' => ['required', 'regex:/^\d{10,12}$/'],
            'formBod' => ['required', 'date'],
            'formLastEducation' => ['required', 'in:smk,diploma,s1,s2,s3'],
            'formMajor' => ['required', 'string', 'max:255'],
            'formIsExperience' => ['required', 'in:0,1'],
            'formCurrentSalary' => ['required', 'string', 'max:20'],
            'formExpectSalary' => ['required', 'string', 'max:20'],
            'formCV' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'totalRow' => ['nullable', 'integer', 'min:0'],
        ]);

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
        $path = $cvId . "." . $ext;
        $absolutePath = app_path("Uploads/" . $path);

        if(! $cv->move(app_path("Uploads"), $path)) {
            return back()->withErrors([
                'formCV' => 'CV gagal diunggah. Silakan coba kembali.',
            ])->withInput();
        }

        Media::create([
            'mediaId' => $cvId,
            'mediaType' => 'application/pdf',
            'mediaExt' => $ext,
            'resultPath' => $path
        ]);

        $this->compressCvIfPossible($cvId, $absolutePath);

        $form["cvid"] = $cvId;
        $form["experiencelist"] = json_encode($expList);

        CareerApply::create($form);

        return back()->with("success", "Anda Berhasil Melamar Pekerjaan Ini.");
    }

    protected function compressCvIfPossible(string $mediaId, string $absolutePath): void
    {
        if (! File::exists($absolutePath)) {
            return;
        }

        $publicUrl = $this->resolvePublicResourceUrl($mediaId);
        if ($publicUrl === null) {
            return;
        }

        $result = $this->pdfCompressionService->compressFromPublicUrl($publicUrl);

        if (! is_array($result)) {
            return;
        }

        $binary = (string) ($result['binary'] ?? '');
        if ($binary === '') {
            return;
        }

        $originalSize = (int) (File::size($absolutePath) ?: 0);
        $compressedSize = strlen($binary);

        if ($originalSize > 0 && $compressedSize >= $originalSize) {
            return;
        }

        File::put($absolutePath, $binary);
    }

    protected function resolvePublicResourceUrl(string $mediaId): ?string
    {
        $relativePath = route('main.getResource', ['id' => $mediaId], false);
        $baseUrl = rtrim((string) config('app.url'), '/');

        if ($baseUrl === '') {
            Log::warning('Skipping PDF compression because APP_URL is empty.');
            return null;
        }

        $publicUrl = $baseUrl . $relativePath;

        if (! filter_var($publicUrl, FILTER_VALIDATE_URL)) {
            Log::warning('Skipping PDF compression because generated URL is invalid.', [
                'url' => $publicUrl,
            ]);
            return null;
        }

        $host = strtolower((string) parse_url($publicUrl, PHP_URL_HOST));
        if ($host === '' || in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with($host, '.local')) {
            Log::info('Skipping PDF compression because resource URL is local.', [
                'url' => $publicUrl,
            ]);
            return null;
        }

        if (filter_var($host, FILTER_VALIDATE_IP)) {
            $isPublicIp = filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
            if ($isPublicIp === false) {
                Log::info('Skipping PDF compression because resource URL uses private/reserved IP.', [
                    'url' => $publicUrl,
                ]);
                return null;
            }
        }

        return $publicUrl;
    }
}
