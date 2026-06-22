@extends('layouts.site')

@php
    $carPath = '/'.$brand->slug.'/'.$car->slug;

    $extractYoutubeId = static function (?string $value): ?string {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('~^[A-Za-z0-9_-]{11}$~', $value) === 1) {
            return $value;
        }

        $parts = parse_url($value);
        if (! is_array($parts)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = (string) ($parts['path'] ?? '');

        if (str_contains($host, 'youtu.be')) {
            $candidate = trim($path, '/');

            return preg_match('~^[A-Za-z0-9_-]{11}$~', $candidate) === 1 ? $candidate : null;
        }

        if (str_contains($host, 'youtube.com')) {
            parse_str((string) ($parts['query'] ?? ''), $query);
            $candidate = (string) ($query['v'] ?? '');

            if (preg_match('~^[A-Za-z0-9_-]{11}$~', $candidate) === 1) {
                return $candidate;
            }

            if (str_starts_with($path, '/embed/')) {
                $candidate = trim(substr($path, strlen('/embed/')), '/');

                return preg_match('~^[A-Za-z0-9_-]{11}$~', $candidate) === 1 ? $candidate : null;
            }
        }

        return null;
    };

    $crashTestYear = $car->crashTest?->year;
    $crashTestRating = $car->crashTest?->rating;
    $hasCrashTestMeta = filled($crashTestYear) || ! is_null($crashTestRating);
    $crashTestVideoId = $extractYoutubeId($car->crashTest?->video_path);
    $firstTestDriveVideoPath = $car->testDrives->first()?->video_path;
    $testDriveVideoId = $extractYoutubeId($firstTestDriveVideoPath);
    $testDrivePreview = filled($testDriveVideoId)
        ? 'https://i.ytimg.com/vi/'.$testDriveVideoId.'/hqdefault.jpg'
        : $car->coverUrl();
@endphp

@section('title', $car->name.' — краш-тест')

@section('content')
<div class="block1" style="margin-bottom: 32px; padding-bottom: 32px;">
    <div class="hleb"><a href="/{{ $brand->slug }}/">Автомобили {{ $brand->name }}</a></div>

    <h1>
        <a href="{{ $carPath }}/">
            {{ $car->name }}
        </a>
        › Краш-тест
    </h1>

    <div class="p_crash_test">
        <div class="crash-test-intro">
            Краш-тест {{ $car->name }}: видео независимой оценки безопасности нового автомобиля.
        </div>

        @if ($hasCrashTestMeta)
            <div class="crash-test-meta" aria-label="Данные краш-теста">
                @if (filled($crashTestYear))
                    <div class="crash-test-meta__item">
                        <span class="crash-test-meta__label">Год проведения</span>
                        <span class="crash-test-meta__value">{{ $crashTestYear }} года</span>
                    </div>
                @endif

                @if (! is_null($crashTestRating))
                    <div class="crash-test-meta__item crash-test-meta__item--accent">
                        <span class="crash-test-meta__label">Результат</span>
                        <span class="crash-test-meta__value">{{ $crashTestRating }} из 5</span>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="video">
        @if (filled($crashTestVideoId))
            <div
                class="youtube"
                data-youtube-id="{{ $crashTestVideoId }}"
                role="button"
                tabindex="0"
                aria-label="Смотреть видео краш-теста {{ $car->name }}"
                style="background-image: url('https://i.ytimg.com/vi/{{ $crashTestVideoId }}/hqdefault.jpg');"
            >
                <div class="play"></div>
            </div>
        @else
            <img alt="Краш-тест {{ $car->name }}" src="{{ $car->coverUrl() }}" data-car-image="true">
        @endif
    </div>
</div>

@if ($car->testDrives->isNotEmpty())
    <div class="block_video" style="grid-template-columns: 1fr; max-width: 600px; margin: 0 auto!important; marign-bottom: 32px; padding-bottom: 32px;">
        <div class="testdrive_div">
            <div class="testdrive_h"><a href="{{ $carPath }}/test-drive/">Тест-драйв</a></div>
            <div class="testdrive_a">
                <a href="{{ $carPath }}/test-drive/">
                    <div class="youtube" style="background-image: url('{{ $testDrivePreview }}');">
                        <div class="play"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.video .youtube').forEach(function (preview) {
        var activatePreview = function () {
            var videoId = preview.getAttribute('data-youtube-id');
            if (!videoId) {
                return;
            }

            var iframe = document.createElement('iframe');
            iframe.setAttribute('src', 'https://www.youtube.com/embed/' + encodeURIComponent(videoId) + '?autoplay=1&rel=0');
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
            iframe.setAttribute('allowfullscreen', 'allowfullscreen');
            iframe.style.width = '100%';
            iframe.style.height = '100%';

            preview.replaceWith(iframe);
        };

        preview.addEventListener('click', activatePreview, { once: true });
        preview.addEventListener('keydown', function (event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            activatePreview();
        }, { once: true });
    });
});
</script>

@include('site.car.partials.galery', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
    'galleryBlockId' => 'crash-test-gallery',
])

@include('site.car.partials.specs-and-models', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
])
@endsection
