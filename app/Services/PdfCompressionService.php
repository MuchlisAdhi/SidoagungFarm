<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PdfCompressionService
{
    /**
     * Compress PDF from public URL using apdf.io and return binary result.
     *
     * @return array{binary:string,size_original:int,size_compressed:int,file_url:string}|null
     */
    public function compressFromPublicUrl(string $fileUrl): ?array
    {
        $token = trim((string) config('services.apdf.token'));
        if ($token === '') {
            return null;
        }

        $endpoint = trim((string) config('services.apdf.compress_endpoint', 'https://apdf.io/api/pdf/file/compress'));
        $timeout = max(5, (int) config('services.apdf.timeout', 30));

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->withToken($token)
                ->timeout($timeout)
                ->post($endpoint, [
                    'file' => $fileUrl,
                ]);

            if (! $response->successful()) {
                Log::warning('apdf compress API failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            }

            $payload = $response->json();
            $compressedFileUrl = is_array($payload) ? (string) ($payload['file'] ?? '') : '';
            if ($compressedFileUrl === '' || ! filter_var($compressedFileUrl, FILTER_VALIDATE_URL)) {
                Log::warning('apdf compress API returned invalid payload.', [
                    'payload' => $payload,
                ]);

                return null;
            }

            $download = Http::timeout($timeout)->get($compressedFileUrl);
            if (! $download->successful()) {
                Log::warning('Failed to download compressed PDF from apdf.', [
                    'status' => $download->status(),
                    'url' => $compressedFileUrl,
                ]);

                return null;
            }

            $binary = $download->body();
            if ($binary === '' || ! str_contains(substr($binary, 0, 1024), '%PDF-')) {
                Log::warning('Downloaded compressed file is not a valid PDF.', [
                    'url' => $compressedFileUrl,
                ]);

                return null;
            }

            return [
                'binary' => $binary,
                'size_original' => (int) ($payload['size_original'] ?? 0),
                'size_compressed' => (int) ($payload['size_compressed'] ?? strlen($binary)),
                'file_url' => $compressedFileUrl,
            ];
        } catch (\Throwable $th) {
            Log::warning('PDF compression via apdf failed with exception.', [
                'message' => $th->getMessage(),
            ]);

            return null;
        }
    }
}
