<?php

namespace App\Services\Admin;

use App\Models\Career;
use App\Models\CareerApply;
use App\Models\Media;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CareerFeedbackService
{
    public function getCareerListWithApplicantCount(): Collection
    {
        $this->closeExpiredCareers();

        return DB::table('career as cr')
            ->select(
                DB::raw('cr.*'),
                DB::raw('(select count(apl.id) from careerapply as apl where apl.careerid = cr.id) as applicants')
            )
            ->get();
    }

    public function getApplicantsByEncryptedCareerId(string $encryptedCareerId): ?array
    {
        $careerId = $this->decryptId($encryptedCareerId);
        if (! $careerId) {
            return null;
        }

        $career = Career::find($careerId);
        if (! $career) {
            return null;
        }

        return [
            'career' => $career,
            'applicants' => CareerApply::where('careerid', $career->id)->get(),
        ];
    }

    public function findApplicantByEncryptedId(string $encryptedApplicantId): ?CareerApply
    {
        $id = $this->decryptId($encryptedApplicantId);
        if (! $id) {
            return null;
        }

        return CareerApply::find($id);
    }

    public function approveApplicant(string $encryptedApplicantId): bool
    {
        $id = $this->decryptId($encryptedApplicantId);
        if (! $id) {
            return false;
        }

        return CareerApply::where('id', $id)->update([
            'isapprove' => 1,
            'rejectreason' => '',
        ]) > 0;
    }

    public function rejectApplicant(string $encryptedApplicantId, string $reason): bool
    {
        $id = $this->decryptId($encryptedApplicantId);
        if (! $id) {
            return false;
        }

        return CareerApply::where('id', $id)->update([
            'isapprove' => 0,
            'rejectreason' => $reason,
        ]) > 0;
    }

    public function findCareerByEncryptedId(string $encryptedCareerId): ?Career
    {
        $careerId = $this->decryptId($encryptedCareerId);
        if (! $careerId) {
            return null;
        }

        return Career::find($careerId);
    }

    public function resolveCvDownload(string $encryptedApplicantId): array
    {
        $applicant = $this->findApplicantByEncryptedId($encryptedApplicantId);
        if (! $applicant || ! $applicant->cvid) {
            return [
                'code' => 404,
                'msg' => 'CV not found',
            ];
        }

        $media = Media::where('mediaId', $applicant->cvid)->first();
        if ($media) {
            $absolutePath = app_path('Uploads/' . $media->resultPath);
            if (File::exists($absolutePath)) {
                return [
                    'code' => 200,
                    'type' => 'local',
                    'path' => $absolutePath,
                    'filename' => basename((string) $media->resultPath),
                ];
            }
        }

        $storagePath = 'public/' . $applicant->cvid;
        if (Storage::exists($storagePath)) {
            return [
                'code' => 200,
                'type' => 'storage',
                'path' => $storagePath,
                'filename' => (string) $applicant->cvid,
            ];
        }

        return [
            'code' => 404,
            'msg' => 'CV file not found in storage',
        ];
    }

    protected function closeExpiredCareers(): void
    {
        $now = now()->timestamp;
        $rows = Career::where('publish', 1)->get();

        foreach ($rows as $career) {
            try {
                $closingDate = Carbon::parse((string) $career->closingdate)->timestamp;
            } catch (Throwable) {
                continue;
            }

            if ($now > $closingDate) {
                $career->update([
                    'publish' => 0,
                ]);
            }
        }
    }

    protected function decryptId(string $encryptedId): ?string
    {
        try {
            return (string) decrypt($encryptedId);
        } catch (Throwable) {
            return null;
        }
    }
}
