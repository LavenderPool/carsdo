<?php

namespace App\Support\Media;

use App\Models\MediaAlias;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaVariantService
{
    private const VARIANT_WEBP = 'webp';

    /**
     * @return array{source_path: string, alias: MediaAlias|null}
     */
    public function storeUploadedFile(
        UploadedFile $file,
        string $directory,
        ?string $ownerType = null,
        ?int $ownerId = null,
    ): array {
        $sourcePath = $file->store($directory, 'public');
        $alias = $this->ensureWebpVariant($sourcePath, $ownerType, $ownerId);

        return [
            'source_path' => $sourcePath,
            'alias' => $alias,
        ];
    }

    public function ensureWebpVariant(
        ?string $sourcePath,
        ?string $ownerType = null,
        ?int $ownerId = null,
    ): ?MediaAlias {
        $normalizedPath = MediaPath::publicDiskPath($sourcePath);

        if ($normalizedPath === null) {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($normalizedPath)) {
            $this->deleteVariants($normalizedPath);

            return null;
        }

        $this->deleteStaleOwnerAliases($normalizedPath, $ownerType, $ownerId);

        $existing = MediaAlias::query()
            ->where('disk', 'public')
            ->where('source_path', $normalizedPath)
            ->where('variant', self::VARIANT_WEBP)
            ->first();

        $sourceAbsolutePath = $disk->path($normalizedPath);
        $sourceMTime = @filemtime($sourceAbsolutePath) ?: time();

        if ($existing !== null && $disk->exists($existing->alias_path)) {
            $aliasAbsolutePath = $disk->path($existing->alias_path);
            $aliasMTime = @filemtime($aliasAbsolutePath) ?: 0;

            if ($aliasMTime >= $sourceMTime) {
                if ($ownerType !== null && $ownerId !== null && ($existing->owner_type !== $ownerType || $existing->owner_id !== $ownerId)) {
                    $existing->forceFill([
                        'owner_type' => $ownerType,
                        'owner_id' => $ownerId,
                    ])->save();
                }

                return $existing;
            }
        }

        $binary = $disk->get($normalizedPath);
        $imageSize = @getimagesizefromstring($binary);

        if ($imageSize === false) {
            return null;
        }

        $sourceMime = strtolower((string) ($imageSize['mime'] ?? ''));

        if ($sourceMime === 'image/webp') {
            $this->deleteVariants($normalizedPath);

            return null;
        }

        $encoded = $this->encodeBestWebp($binary, $sourceMime);

        if ($encoded === null) {
            return null;
        }

        $aliasPath = $this->makeAliasPath($normalizedPath, $sourceMTime);
        $disk->makeDirectory(dirname($aliasPath));
        $disk->put($aliasPath, $encoded['contents']);

        if ($existing !== null && $existing->alias_path !== $aliasPath) {
            $disk->delete($existing->alias_path);
        }

        return MediaAlias::query()->updateOrCreate(
            [
                'disk' => 'public',
                'source_path' => $normalizedPath,
                'variant' => self::VARIANT_WEBP,
            ],
            [
                'alias_path' => $aliasPath,
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'mime_type' => 'image/webp',
                'width' => $encoded['width'],
                'height' => $encoded['height'],
                'file_size' => strlen($encoded['contents']),
                'quality' => $encoded['quality'],
            ],
        );
    }

    public function resolvePreferredUrl(
        ?string $sourcePath,
        ?string $fallbackUrl = null,
        bool $generateIfMissing = false,
        ?string $ownerType = null,
        ?int $ownerId = null,
    ): ?string {
        $normalizedPath = MediaPath::publicDiskPath($sourcePath);

        if ($normalizedPath === null) {
            return $fallbackUrl;
        }

        $alias = $generateIfMissing
            ? $this->ensureWebpVariant($normalizedPath, $ownerType, $ownerId)
            : MediaAlias::query()
                ->where('disk', 'public')
                ->where('source_path', $normalizedPath)
                ->where('variant', self::VARIANT_WEBP)
                ->first();

        if ($alias !== null && Storage::disk('public')->exists($alias->alias_path)) {
            return MediaPath::publicUrl($alias->alias_path);
        }

        return $fallbackUrl;
    }

    public function resolveVariantAbsolutePath(
        ?string $sourcePath,
        bool $generateIfMissing = false,
        ?string $ownerType = null,
        ?int $ownerId = null,
    ): ?string {
        $normalizedPath = MediaPath::publicDiskPath($sourcePath);

        if ($normalizedPath === null) {
            return null;
        }

        $alias = $generateIfMissing
            ? $this->ensureWebpVariant($normalizedPath, $ownerType, $ownerId)
            : MediaAlias::query()
                ->where('disk', 'public')
                ->where('source_path', $normalizedPath)
                ->where('variant', self::VARIANT_WEBP)
                ->first();

        if ($alias === null || !Storage::disk('public')->exists($alias->alias_path)) {
            return null;
        }

        return Storage::disk('public')->path($alias->alias_path);
    }

    public function deleteVariants(?string $sourcePath): void
    {
        $normalizedPath = MediaPath::publicDiskPath($sourcePath);

        if ($normalizedPath === null) {
            return;
        }

        $aliases = MediaAlias::query()
            ->where('disk', 'public')
            ->where('source_path', $normalizedPath)
            ->get();

        if ($aliases->isEmpty()) {
            return;
        }

        Storage::disk('public')->delete($aliases->pluck('alias_path')->filter()->all());
        MediaAlias::query()->whereKey($aliases->pluck('id')->all())->delete();
    }

    public function deleteVariantsForOwner(?string $ownerType, ?int $ownerId): void
    {
        if ($ownerType === null || $ownerId === null) {
            return;
        }

        $aliases = MediaAlias::query()
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->get();

        if ($aliases->isEmpty()) {
            return;
        }

        Storage::disk('public')->delete($aliases->pluck('alias_path')->filter()->all());
        MediaAlias::query()->whereKey($aliases->pluck('id')->all())->delete();
    }

    private function deleteStaleOwnerAliases(string $sourcePath, ?string $ownerType, ?int $ownerId): void
    {
        if ($ownerType === null || $ownerId === null) {
            return;
        }

        $staleAliases = MediaAlias::query()
            ->where('owner_type', $ownerType)
            ->where('owner_id', $ownerId)
            ->where('source_path', '!=', $sourcePath)
            ->get();

        if ($staleAliases->isEmpty()) {
            return;
        }

        Storage::disk('public')->delete($staleAliases->pluck('alias_path')->filter()->all());
        MediaAlias::query()->whereKey($staleAliases->pluck('id')->all())->delete();
    }

    private function makeAliasPath(string $sourcePath, int $sourceMTime): string
    {
        $directory = trim(dirname($sourcePath), '.');
        $filename = pathinfo($sourcePath, PATHINFO_FILENAME);
        $signature = substr(sha1($sourcePath.'|'.$sourceMTime), 0, 12);

        return ($directory !== '' ? $directory.'/' : '').'variants/'.$filename.'-'.$signature.'.webp';
    }

    /**
     * @return array{contents: string, quality: int, width: int, height: int}|null
     */
    private function encodeBestWebp(string $binary, string $sourceMime): ?array
    {
        $qualities = $this->qualityCandidates($sourceMime, strlen($binary));
        $best = null;

        foreach ($qualities as $quality) {
            $candidate = $this->encodeWithGd($binary, $quality) ?? $this->encodeWithImagick($binary, $quality);

            if ($candidate === null) {
                continue;
            }

            $candidate['quality'] = $quality;
            $best ??= $candidate;

            if (strlen($candidate['contents']) <= (int) floor(strlen($binary) * 0.85)) {
                return $candidate;
            }

            if (strlen($candidate['contents']) < strlen($best['contents'])) {
                $best = $candidate;
            }
        }

        return $best;
    }

    /**
     * @return array{contents: string, width: int, height: int}|null
     */
    private function encodeWithGd(string $binary, int $quality): ?array
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagewebp')) {
            return null;
        }

        $image = @imagecreatefromstring($binary);

        if ($image === false) {
            return null;
        }

        if (function_exists('imagepalettetotruecolor')) {
            @imagepalettetotruecolor($image);
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        ob_start();
        $encoded = imagewebp($image, null, $quality);
        $contents = (string) ob_get_clean();
        $width = imagesx($image);
        $height = imagesy($image);
        imagedestroy($image);

        if ($encoded === false || $contents === '') {
            return null;
        }

        return [
            'contents' => $contents,
            'width' => $width,
            'height' => $height,
        ];
    }

    /**
     * @return array{contents: string, width: int, height: int}|null
     */
    private function encodeWithImagick(string $binary, int $quality): ?array
    {
        if (!class_exists('\\Imagick')) {
            return null;
        }

        try {
            $image = new \Imagick();
            $image->readImageBlob($binary);
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($quality);
            $image->setImageAlphaChannel(\Imagick::ALPHACHANNEL_ACTIVATE);

            $contents = $image->getImagesBlob();
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();
            $image->clear();
            $image->destroy();

            if ($contents === '') {
                return null;
            }

            return [
                'contents' => $contents,
                'width' => $width,
                'height' => $height,
            ];
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return list<int>
     */
    private function qualityCandidates(string $sourceMime, int $sourceSize): array
    {
        if ($sourceMime === 'image/png') {
            return [86, 82, 78, 74, 70];
        }

        if ($sourceSize >= 3 * 1024 * 1024) {
            return [78, 74, 70, 66, 62];
        }

        if ($sourceSize >= 1024 * 1024) {
            return [82, 78, 74, 70, 66];
        }

        return [84, 80, 76, 72, 68];
    }
}
