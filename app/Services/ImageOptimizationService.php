<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Spatie\ImageOptimizer\OptimizerChain;
use Symfony\Component\Process\Process;

class ImageOptimizationService
{
    protected static bool $binaryPrepared = false;
    protected static bool $skipBinaryFallback = false;

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

        $this->prepareBinaryAliases();

        $beforeSize = (int) (filesize($absolutePath) ?: 0);
        $optimizedBySpatie = false;

        try {
            app(OptimizerChain::class)->optimize($absolutePath);
            $optimizedBySpatie = true;
        } catch (\Throwable $th) {
            Log::warning('Spatie image optimizer failed, fallback will be attempted.', [
                'path' => $absolutePath,
                'ext' => $ext,
                'error' => $th->getMessage(),
            ]);
        }

        $afterSpatieSize = (int) (filesize($absolutePath) ?: 0);
        if ($this->shouldRunGdFallback($ext, $beforeSize, $afterSpatieSize, $optimizedBySpatie)) {
            $this->optimizeWithGd($absolutePath, $ext);
        }

        $afterGdSize = (int) (filesize($absolutePath) ?: 0);
        if ($afterGdSize <= 0 || $afterGdSize >= $beforeSize) {
            $this->optimizeWithBinaryFallback($absolutePath, $ext);
        }

        $afterFinalSize = (int) (filesize($absolutePath) ?: 0);
        if ($beforeSize > 0 && $afterFinalSize > 0) {
            $delta = $beforeSize - $afterFinalSize;
            $percent = round(($delta / $beforeSize) * 100, 2);
            if ($delta > 0) {
                Log::info('Image optimized successfully.', [
                    'path' => $absolutePath,
                    'ext' => $ext,
                    'before' => $beforeSize,
                    'after' => $afterFinalSize,
                    'saved_bytes' => $delta,
                    'saved_percent' => $percent,
                ]);
            } else {
                Log::warning('Image optimization gave no size reduction.', [
                    'path' => $absolutePath,
                    'ext' => $ext,
                    'before' => $beforeSize,
                    'after' => $afterFinalSize,
                ]);
            }
        }
    }

    protected function optimizeWithBinaryFallback(string $absolutePath, string $ext): void
    {
        if (self::$skipBinaryFallback) {
            return;
        }

        $commands = $this->buildBinaryCommands($absolutePath, $ext);
        if ($commands === []) {
            return;
        }

        foreach ($commands as $command) {
            $binary = $command['binary'];
            $binaryName = $command['binary_name'];
            $args = $command['args'];

            if (! is_file($binary)) {
                $this->runGlobalBinaryFallback($binaryName, $args, $absolutePath, $ext, true);

                Log::warning('Binary optimizer not found (custom path).', [
                    'binary' => $binary,
                    'path' => $absolutePath,
                    'ext' => $ext,
                ]);
                continue;
            }

            $process = new Process(array_merge([$binary], $args));
            $process->setTimeout(60);
            $process->run();

            if (! $process->isSuccessful()) {
                $errorOutput = trim((string) $process->getErrorOutput());

                if ($this->containsGlibcError($errorOutput)) {
                    $globalWorked = $this->runGlobalBinaryFallback($binaryName, $args, $absolutePath, $ext, false);
                    if ($globalWorked) {
                        continue;
                    }

                    self::$skipBinaryFallback = true;
                    Log::warning('Disabling binary fallback due to GLIBC incompatibility.', [
                        'binary' => $binary,
                        'path' => $absolutePath,
                        'ext' => $ext,
                        'error' => $errorOutput,
                    ]);
                }

                Log::warning('Binary optimizer command failed.', [
                    'binary' => $binary,
                    'args' => $args,
                    'path' => $absolutePath,
                    'ext' => $ext,
                    'error' => $errorOutput,
                ]);
            }
        }
    }

    protected function shouldRunGdFallback(string $ext, int $beforeSize, int $afterSpatieSize, bool $optimizedBySpatie): bool
    {
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return false;
        }

        if (! function_exists('imagejpeg') || ! function_exists('imagepng')) {
            return false;
        }

        if (! $optimizedBySpatie) {
            return true;
        }

        return $afterSpatieSize <= 0 || $afterSpatieSize >= $beforeSize;
    }

    protected function optimizeWithGd(string $absolutePath, string $ext): void
    {
        $memoryLimitBefore = ini_get('memory_limit');

        if (! $this->canUseGdSafely($absolutePath)) {
            $raised = $this->tryRaiseMemoryLimitForOptimization();

            if (! $raised || ! $this->canUseGdSafely($absolutePath)) {
                Log::warning('Skipping GD fallback due to memory limit risk.', [
                    'path' => $absolutePath,
                    'ext' => $ext,
                    'memory_limit' => ini_get('memory_limit'),
                ]);

                if ($memoryLimitBefore !== false && $memoryLimitBefore !== null) {
                    @ini_set('memory_limit', (string) $memoryLimitBefore);
                }

                return;
            }
        }

        try {
            if (in_array($ext, ['jpg', 'jpeg'], true)) {
                $image = @imagecreatefromjpeg($absolutePath);
                if (! $image) {
                    return;
                }

                imageinterlace($image, true);
                imagejpeg($image, $absolutePath, 82);
                imagedestroy($image);
                return;
            }

            if ($ext === 'png') {
                $image = @imagecreatefrompng($absolutePath);
                if (! $image) {
                    return;
                }

                if (function_exists('imagetruecolortopalette') && function_exists('imageistruecolor') && imageistruecolor($image)) {
                    @imagetruecolortopalette($image, true, 255);
                }

                imagealphablending($image, false);
                imagesavealpha($image, true);
                imagepng($image, $absolutePath, 8);
                imagedestroy($image);
                return;
            }

            if ($ext === 'webp' && function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
                $image = @imagecreatefromwebp($absolutePath);
                if (! $image) {
                    return;
                }

                imagewebp($image, $absolutePath, 82);
                imagedestroy($image);
            }
        } catch (\Throwable $th) {
            Log::warning('GD image fallback optimization failed.', [
                'path' => $absolutePath,
                'ext' => $ext,
                'error' => $th->getMessage(),
            ]);
        } finally {
            if ($memoryLimitBefore !== false && $memoryLimitBefore !== null) {
                @ini_set('memory_limit', (string) $memoryLimitBefore);
            }
        }
    }

    protected function prepareBinaryAliases(): void
    {
        if (self::$binaryPrepared) {
            return;
        }

        self::$binaryPrepared = true;

        $binaryPath = (string) config('image-optimizer.binary_path', '');
        if ($binaryPath === '') {
            return;
        }

        $binaryPath = rtrim($binaryPath, '/\\') . DIRECTORY_SEPARATOR;
        $optipng = $binaryPath . 'optipng';
        $opitpng = $binaryPath . 'opitpng';

        if (! is_file($optipng) && is_file($opitpng)) {
            try {
                if (@copy($opitpng, $optipng)) {
                    @chmod($optipng, 0755);
                    Log::warning('Binary alias applied for optipng (from opitpng).', [
                        'source' => $opitpng,
                        'target' => $optipng,
                    ]);
                }
            } catch (\Throwable $th) {
                Log::warning('Failed to apply binary alias for optipng.', [
                    'source' => $opitpng,
                    'target' => $optipng,
                    'error' => $th->getMessage(),
                ]);
            }
        }
    }

    protected function buildBinaryCommands(string $absolutePath, string $ext): array
    {
        $binaryPath = (string) config('image-optimizer.binary_path', '');
        if ($binaryPath === '') {
            return [];
        }

        $binaryPath = rtrim($binaryPath, '/\\') . DIRECTORY_SEPARATOR;
        $commands = [];

        if (in_array($ext, ['jpg', 'jpeg'], true)) {
            $commands[] = [
                'binary' => $binaryPath . 'jpegoptim',
                'binary_name' => 'jpegoptim',
                'args' => ['-m85', '--strip-all', '--all-progressive', $absolutePath],
            ];
        }

        if ($ext === 'png') {
            $commands[] = [
                'binary' => $binaryPath . 'pngquant',
                'binary_name' => 'pngquant',
                'args' => ['--force', '--skip-if-larger', '--quality=65-85', '--output', $absolutePath, $absolutePath],
            ];
            $commands[] = [
                'binary' => $binaryPath . 'optipng',
                'binary_name' => 'optipng',
                'args' => ['-i0', '-o2', '-quiet', $absolutePath],
            ];
        }

        if ($ext === 'gif') {
            $commands[] = [
                'binary' => $binaryPath . 'gifsicle',
                'binary_name' => 'gifsicle',
                'args' => ['-b', '-O3', $absolutePath],
            ];
        }

        return $commands;
    }

    protected function canUseGdSafely(string $absolutePath): bool
    {
        $size = @getimagesize($absolutePath);
        if (! is_array($size) || count($size) < 2) {
            return false;
        }

        $width = (int) $size[0];
        $height = (int) $size[1];
        if ($width <= 0 || $height <= 0) {
            return false;
        }

        $memoryLimitBytes = $this->toBytes((string) ini_get('memory_limit'));
        if ($memoryLimitBytes <= 0) {
            return true;
        }

        $currentUsage = memory_get_usage(true);
        $fileSize = (int) (filesize($absolutePath) ?: 0);
        $estimatedBytes = (int) (($width * $height * 8) + ($fileSize * 2) + (64 * 1024 * 1024));
        $headroom = (int) ($memoryLimitBytes - $currentUsage - (96 * 1024 * 1024));

        return $estimatedBytes > 0 && $estimatedBytes < $headroom;
    }

    protected function containsGlibcError(string $errorOutput): bool
    {
        return str_contains($errorOutput, 'GLIBC_')
            || str_contains($errorOutput, 'not found (required by')
            || str_contains($errorOutput, 'version `GLIBC_');
    }

    protected function runGlobalBinaryFallback(
        string $binaryName,
        array $args,
        string $absolutePath,
        string $ext,
        bool $silentWhenFail
    ): bool {
        $process = new Process(array_merge([$binaryName], $args));
        $process->setTimeout(60);
        $process->run();

        if ($process->isSuccessful()) {
            Log::info('Global binary optimizer command succeeded.', [
                'binary' => $binaryName,
                'args' => $args,
                'path' => $absolutePath,
                'ext' => $ext,
            ]);

            return true;
        }

        if (! $silentWhenFail) {
            Log::warning('Global binary optimizer command failed.', [
                'binary' => $binaryName,
                'args' => $args,
                'path' => $absolutePath,
                'ext' => $ext,
                'error' => trim((string) $process->getErrorOutput()),
            ]);
        }

        return false;
    }

    protected function tryRaiseMemoryLimitForOptimization(): bool
    {
        $targetLimit = (string) env('IMAGE_OPTIMIZER_GD_MEMORY_LIMIT', '1024M');
        if ($targetLimit === '') {
            return false;
        }

        $before = (string) ini_get('memory_limit');
        @ini_set('memory_limit', $targetLimit);
        $after = (string) ini_get('memory_limit');

        return $after !== '' && $after !== $before;
    }

    protected function toBytes(string $value): int
    {
        $value = trim($value);
        if ($value === '' || $value === '-1') {
            return -1;
        }

        $last = strtolower(substr($value, -1));
        $number = (float) $value;
        return match ($last) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    }
}
