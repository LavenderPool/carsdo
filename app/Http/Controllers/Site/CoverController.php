<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Support\Media\MediaVariantService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CoverController extends Controller
{
    public function __invoke(string $brand_slug, string $car_slug): BinaryFileResponse
    {
        $sourcePath = "covers/{$brand_slug}/{$car_slug}/cover.jpg";
        $variantPath = app(MediaVariantService::class)->resolveVariantAbsolutePath($sourcePath, false);
        $coverPath = $variantPath ?? Storage::disk('public')->path($sourcePath);

        abort_unless(is_file($coverPath), 404);

        $headers = $variantPath !== null ? ['Content-Type' => 'image/webp'] : [];

        return response()->file($coverPath, $headers);
    }
}
