<?php

namespace App\Support\Assets;

use Illuminate\Support\Facades\File;

class CssAssetService
{
    public function versionedUrl(string $relativePath): string
    {
        $sourceAbsolutePath = public_path($relativePath);

        if (!is_file($sourceAbsolutePath)) {
            return asset(ltrim($relativePath, '/'));
        }

        $sourceMTime = filemtime($sourceAbsolutePath) ?: time();
        $optimizedRelativePath = $this->ensureMinifiedAsset($relativePath, $sourceMTime);

        return asset(ltrim($optimizedRelativePath, '/')).'?v='.$sourceMTime;
    }

    private function ensureMinifiedAsset(string $relativePath, int $sourceMTime): string
    {
        $normalizedPath = ltrim($relativePath, '/');
        $directoryKey = substr(sha1(dirname($normalizedPath)), 0, 10);
        $filename = pathinfo($normalizedPath, PATHINFO_FILENAME);
        $optimizedRelativePath = "cache-css/{$directoryKey}/{$filename}-{$sourceMTime}.min.css";
        $optimizedAbsolutePath = public_path($optimizedRelativePath);

        if (is_file($optimizedAbsolutePath)) {
            return $optimizedRelativePath;
        }

        File::ensureDirectoryExists(dirname($optimizedAbsolutePath));
        File::put($optimizedAbsolutePath, $this->minify(File::get(public_path($normalizedPath))));

        return $optimizedRelativePath;
    }

    private function minify(string $css): string
    {
        $placeholders = [];
        $index = 0;

        $css = preg_replace_callback(
            '/"(?:\\\\.|[^"\\\\])*"|\'(?:\\\\.|[^\'\\\\])*\'/s',
            static function (array $matches) use (&$placeholders, &$index): string {
                $placeholder = "__CSS_LITERAL_{$index}__";
                $placeholders[$placeholder] = $matches[0];
                $index++;

                return $placeholder;
            },
            $css,
        ) ?? $css;

        $css = preg_replace('~/\*(?!\!)(.*?)\*/~s', '', $css) ?? $css;
        $css = preg_replace('/\s+/', ' ', $css) ?? $css;
        $css = preg_replace('/\s*([{}:;,])\s*/', '$1', $css) ?? $css;
        $css = str_replace(';}', '}', trim($css));

        return strtr($css, $placeholders);
    }
}
