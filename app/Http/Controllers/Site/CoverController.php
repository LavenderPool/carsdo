<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CoverController extends Controller
{
    public function __invoke(string $brand_slug, string $car_slug): BinaryFileResponse
    {
        $coverPath = storage_path("app/public/covers/{$brand_slug}/{$car_slug}/cover.jpg");

        abort_unless(is_file($coverPath), 404);

        return response()->file($coverPath);
    }
}
