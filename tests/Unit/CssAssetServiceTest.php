<?php

namespace Tests\Unit;

use App\Support\Assets\CssAssetService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class CssAssetServiceTest extends TestCase
{
    public function test_it_builds_versioned_url_for_minified_public_css(): void
    {
        $sourceRelativePath = 'tests-assets/example.css';
        $sourceAbsolutePath = public_path($sourceRelativePath);
        $sourceDirectory = dirname($sourceAbsolutePath);

        File::ensureDirectoryExists($sourceDirectory);
        File::put($sourceAbsolutePath, "body {\n    color: red;\n}\n");

        $service = app(CssAssetService::class);
        $url = $service->versionedUrl($sourceRelativePath);

        $this->assertStringContainsString('/cache-css/', $url);
        $this->assertStringContainsString('?v=', $url);

        $relativeGeneratedPath = explode('?', parse_url($url, PHP_URL_PATH) ?? '')[0];
        $generatedAbsolutePath = public_path(ltrim((string) $relativeGeneratedPath, '/'));

        $this->assertFileExists($generatedAbsolutePath);
        $this->assertSame('body{color:red}', trim((string) File::get($generatedAbsolutePath)));

        File::delete($sourceAbsolutePath);
        File::deleteDirectory(public_path('cache-css'));
        File::deleteDirectory($sourceDirectory);
    }
}
