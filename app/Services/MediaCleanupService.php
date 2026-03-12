<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MediaCleanupService
{
    /**
     * Delete file + media row when the media ID is no longer referenced.
     */
    public function cleanupIfUnused(?string $mediaId): void
    {
        if (! $mediaId) {
            return;
        }

        if ($this->isStillReferenced($mediaId)) {
            return;
        }

        $media = Media::where('mediaId', $mediaId)->first();
        if (! $media) {
            return;
        }

        $candidatePaths = array_filter([
            $media->resultPath,
            ($media->mediaExt ? ($mediaId . '.' . $media->mediaExt) : null),
        ]);

        foreach (array_unique($candidatePaths) as $relativePath) {
            $absolutePath = app_path('Uploads/' . ltrim((string) $relativePath, '/\\'));
            if (is_file($absolutePath)) {
                File::delete($absolutePath);
            }
        }

        $media->delete();

        Log::info('Media file cleaned up.', [
            'media_id' => $mediaId,
            'paths' => array_values(array_unique($candidatePaths)),
        ]);
    }

    protected function isStillReferenced(string $mediaId): bool
    {
        $references = [
            ['table' => 'product', 'column' => 'mediaId'],
            ['table' => 'banners', 'column' => 'mediaId'],
            ['table' => 'news', 'column' => 'thumbnail'],
            ['table' => 'resep', 'column' => 'thumbnail'],
            ['table' => 'csr', 'column' => 'thumbnail'],
            ['table' => 'testimoni', 'column' => 'photo'],
            ['table' => 'careerapply', 'column' => 'cvid'],
        ];

        foreach ($references as $reference) {
            if (DB::table($reference['table'])->where($reference['column'], $mediaId)->exists()) {
                return true;
            }
        }

        return false;
    }
}

