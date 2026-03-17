<?php

namespace App\Services;

use App\Models\CareerApply;
use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class CareerApplicationService
{
    public function __construct(
        protected PdfCompressionService $pdfCompressionService
    ) {}

    public function submit(array $payload, array $experienceRows, UploadedFile $cv): CareerApply
    {
        $cvId = (string) Str::uuid();
        $ext = strtolower($cv->getClientOriginalExtension());
        $path = $cvId . '.' . $ext;
        $uploadDirectory = app_path('Uploads');
        $absolutePath = $uploadDirectory . DIRECTORY_SEPARATOR . $path;

        if (! File::isDirectory($uploadDirectory)) {
            File::makeDirectory($uploadDirectory, 0755, true);
        }

        try {
            $cv->move($uploadDirectory, $path);
        } catch (\Throwable) {
            throw new RuntimeException('CV gagal diunggah. Silakan coba kembali.');
        }

        Media::create([
            'mediaId' => $cvId,
            'mediaType' => 'application/pdf',
            'mediaExt' => $ext,
            'resultPath' => $path,
        ]);

        $this->compressCvIfPossible($cvId, $absolutePath);

        return CareerApply::create([
            ...$payload,
            'cvid' => $cvId,
            'experiencelist' => json_encode($experienceRows),
        ]);
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
