<?php

namespace App\Services;

use Spatie\ImageOptimizer\OptimizerChain;

class ImageOptimizationService
{
    /**
     * Optimize image file safely.
     * Failures are logged but will not break the main request flow.
     */
    public function optimize(string $absolutePath, ?string $extension = null): void
    {
        if (! is_file($absolutePath) || ! is_readable($absolutePath)) {
            return;
        }

        $ext = strtolower((string) ($extension ?: pathinfo($absolutePath, PATHINFO_EXTENSION)));
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'], true)) {
            return;
        }

        try {
            app(OptimizerChain::class)->optimize($absolutePath);
        } catch (\Throwable $th) {
            report($th);
        }
    }
}
