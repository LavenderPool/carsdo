@extends('layouts.site')

@php
    $carPath = '/'.$brand->slug.'/'.$car->slug;
    $testDrives = $car->testDrives
        ->sortBy([
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();

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
@endphp

@section('title', $car->name . ' — тест-драйвы')

@section('content')
<div class="block1">
    <div class="hleb"><a href="/{{ $brand->slug }}/">Автомобили {{ $brand->name }}</a></div>

    <h1 style="padding-left:20px;">{{ $pageH1 ?? ($car->name . ' › Тест-драйв') }}</h1>

    <div class="p_test_drive">Подборка видео обзоров {{ $car->name }}: тест-драйвы нового автомобиля.</div>

    @foreach ($testDrives as $testDrive)
        @php
            $youtubeId = $extractYoutubeId($testDrive->video_path);
        @endphp
        @if (filled($youtubeId))
            <div class="name_test_drive">{{ $testDrive->author }}</div>
            <div class="video">
                <div
                    class="youtube"
                    id="{{ $youtubeId }}"
                    style="background-image: url('https://i.ytimg.com/vi/{{ $youtubeId }}/hqdefault.jpg');"
                >
                    <div class="play"></div>
                </div>
            </div>
        @endif
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.video .youtube').forEach(function (preview) {
        preview.addEventListener('click', function () {
            var videoId = preview.getAttribute('id');
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
        }, { once: true });
    });
});
</script>

@include('site.car.partials.galery', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
    'galleryBlockId' => 'test-drive-gallery',
])

@include('site.car.partials.specs-and-models', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
])
@endsection
