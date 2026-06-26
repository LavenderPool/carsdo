<?php

namespace App\Support\Media;

use App\Models\Car;
use App\Models\CarPhoto;
use App\Models\MediaAlias;
use Illuminate\Support\Facades\Storage;

class CarWebpBackfillService
{
    public const SOURCE_TYPE_PHOTO = 'photos';
    public const SOURCE_TYPE_COVER = 'covers';

    /**
     * @return array{
     *     summary: array{total: int, eligible: int, converted: int, pending: int, skipped: int},
     *     sources: array<int, array{key: string, label: string, total: int, eligible: int, converted: int, pending: int, skipped: int}>
     * }
     */
    public function buildPreview(): array
    {
        $rows = $this->collectRows();
        $aliasMap = $this->aliasMap($rows);
        $summary = $this->emptyCounters();
        $sources = [
            self::SOURCE_TYPE_PHOTO => [
                'key' => self::SOURCE_TYPE_PHOTO,
                'label' => 'Фото',
                ...$this->emptyCounters(),
            ],
            self::SOURCE_TYPE_COVER => [
                'key' => self::SOURCE_TYPE_COVER,
                'label' => 'Обложки',
                ...$this->emptyCounters(),
            ],
        ];

        foreach ($rows as $row) {
            $state = $this->resolveRowState($row, $aliasMap);

            $summary['total']++;
            $summary[$state]++;

            if ($state !== 'skipped') {
                $summary['eligible']++;
            }

            $sources[$row['source_type']]['total']++;
            $sources[$row['source_type']][$state]++;

            if ($state !== 'skipped') {
                $sources[$row['source_type']]['eligible']++;
            }
        }

        return [
            'summary' => $summary,
            'sources' => array_values($sources),
        ];
    }

    /**
     * @return array<int, array<int, array{owner_type: class-string, owner_id: int, source_path: string}>>
     */
    public function pendingChunks(int $chunkSize): array
    {
        $rows = $this->collectRows();
        $aliasMap = $this->aliasMap($rows);
        $pending = [];

        foreach ($rows as $row) {
            if ($this->resolveRowState($row, $aliasMap) !== 'pending') {
                continue;
            }

            $pending[] = [
                'owner_type' => $row['owner_type'],
                'owner_id' => $row['owner_id'],
                'source_path' => $row['source_path'],
            ];
        }

        if ($pending === []) {
            return [];
        }

        return array_chunk($pending, max(1, $chunkSize));
    }

    /**
     * @return array<int, array{
     *     source_type: string,
     *     owner_type: class-string,
     *     owner_id: int,
     *     source_path: string|null
     * }>
     */
    private function collectRows(): array
    {
        return [
            ...$this->collectPhotoRows(),
            ...$this->collectCoverRows(),
        ];
    }

    /**
     * @return array<int, array{
     *     source_type: string,
     *     owner_type: class-string,
     *     owner_id: int,
     *     source_path: string|null
     * }>
     */
    private function collectPhotoRows(): array
    {
        $rows = [];

        foreach (CarPhoto::query()->select(['id', 'photo_path'])->cursor() as $photo) {
            $rows[] = [
                'source_type' => self::SOURCE_TYPE_PHOTO,
                'owner_type' => CarPhoto::class,
                'owner_id' => $photo->id,
                'source_path' => MediaPath::publicDiskPath($photo->photo_path),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array{
     *     source_type: string,
     *     owner_type: class-string,
     *     owner_id: int,
     *     source_path: string|null
     * }>
     */
    private function collectCoverRows(): array
    {
        $rows = [];

        foreach (Car::query()->with('brand:id,slug')->select(['id', 'brand_id', 'slug', 'cover_path'])->cursor() as $car) {
            $rows[] = [
                'source_type' => self::SOURCE_TYPE_COVER,
                'owner_type' => Car::class,
                'owner_id' => $car->id,
                'source_path' => $this->resolveCoverSourcePath($car),
            ];
        }

        return $rows;
    }

    private function resolveCoverSourcePath(Car $car): ?string
    {
        if (is_string($car->cover_path) && $car->cover_path !== '') {
            return MediaPath::publicDiskPath($car->cover_path);
        }

        $brandSlug = (string) $car->brand?->slug;
        $carSlug = (string) $car->slug;

        if ($brandSlug === '' || $carSlug === '') {
            return null;
        }

        return "covers/{$brandSlug}/{$carSlug}/cover.jpg";
    }

    /**
     * @param  array<int, array{source_path: string|null}>  $rows
     * @return array<string, string>
     */
    private function aliasMap(array $rows): array
    {
        $sourcePaths = [];

        foreach ($rows as $row) {
            if ($row['source_path'] === null) {
                continue;
            }

            $sourcePaths[$row['source_path']] = $row['source_path'];
        }

        if ($sourcePaths === []) {
            return [];
        }

        return MediaAlias::query()
            ->where('disk', 'public')
            ->where('variant', 'webp')
            ->whereIn('source_path', array_values($sourcePaths))
            ->get(['source_path', 'alias_path'])
            ->mapWithKeys(fn (MediaAlias $alias): array => [$alias->source_path => $alias->alias_path])
            ->all();
    }

    /**
     * @param  array{
     *     source_type: string,
     *     owner_type: class-string,
     *     owner_id: int,
     *     source_path: string|null
     * }  $row
     * @param  array<string, string>  $aliasMap
     */
    private function resolveRowState(array $row, array $aliasMap): string
    {
        if ($row['source_path'] === null) {
            return 'skipped';
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($row['source_path'])) {
            return 'skipped';
        }

        $binary = $disk->get($row['source_path']);
        $imageSize = @getimagesizefromstring($binary);
        $mime = strtolower((string) ($imageSize['mime'] ?? ''));

        if ($mime === 'image/webp') {
            return 'skipped';
        }

        $aliasPath = $aliasMap[$row['source_path']] ?? null;

        if (is_string($aliasPath) && $aliasPath !== '' && $disk->exists($aliasPath)) {
            return 'converted';
        }

        return 'pending';
    }

    /**
     * @return array{total: int, eligible: int, converted: int, pending: int, skipped: int}
     */
    private function emptyCounters(): array
    {
        return [
            'total' => 0,
            'eligible' => 0,
            'converted' => 0,
            'pending' => 0,
            'skipped' => 0,
        ];
    }
}
