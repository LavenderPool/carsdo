@extends('layouts.site')

@php
    $carPath = '/'.$brand->slug.'/'.$car->slug;
    $configurationGroups = $car->configurationGroups
        ->sortBy([
            ['order', 'asc'],
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();
    $configurations = $car->configurations
        ->sortBy([
            ['car_configuration_group_id', 'asc'],
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();
    $photoGroups = $car->photoGroups
        ->map(function ($group) {
            return (object) [
                'name' => filled($group->name) ? $group->name : 'Фотогалерея',
                'photos' => $group->photos
                    ->filter(fn ($photo) => filled($photo->photo_path))
                    ->sortBy('id')
                    ->values(),
            ];
        })
        ->filter(fn ($group) => $group->photos->isNotEmpty())
        ->values();
    $groupedPhotoIds = $photoGroups
        ->flatMap(fn ($group) => $group->photos->pluck('id'))
        ->unique()
        ->all();
    $ungroupedPhotos = $car->photos
        ->filter(fn ($photo) => filled($photo->photo_path) && ! in_array($photo->id, $groupedPhotoIds, true))
        ->sortBy('id')
        ->values();
    $photoSections = $photoGroups->values();

    if ($ungroupedPhotos->isNotEmpty()) {
        $photoSections = $photoSections->push((object) [
            'name' => 'Фото',
            'photos' => $ungroupedPhotos,
        ]);
    }

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

    $crashTestYoutubeId = $extractYoutubeId($car->crashTest?->video_path);
    $crashTestPreview = filled($crashTestYoutubeId)
        ? 'https://i.ytimg.com/vi/'.$crashTestYoutubeId.'/hqdefault.jpg'
        : $car->coverUrl();
    $firstTestDriveVideoPath = $car->testDrives->first()?->video_path;
    $testDriveYoutubeId = $extractYoutubeId($firstTestDriveVideoPath);
    $testDrivePreview = filled($testDriveYoutubeId)
        ? 'https://i.ytimg.com/vi/'.$testDriveYoutubeId.'/hqdefault.jpg'
        : $car->coverUrl();
@endphp

@section('title', 'Фото ' . $brand->name . ' ' . $car->name)

@section('content')
<div class="block1">
    <div class="hleb"><a href="/{{ $brand->slug }}/">Автомобили {{ $brand->name }}</a></div>

    <h1 style="padding-left:20px;"><a href="{{ $carPath }}/">{{ $brand->name }} {{ $car->name }}</a> › Фото</h1>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css">

    <div class="photo_gallery_bg">
        <div><div class="photo_text"><a href="{{ $carPath }}/">Новые комплектации и цены {{ $brand->name }} {{ $car->name }}</a></div></div>

        @forelse ($photoSections as $sectionIndex => $photoSection)
            <h2 style="text-align:center; padding:5px 0 10px;">{{ mb_strtoupper($photoSection->name) }}</h2>

            <div class="photogallerybox">
                <div class="gallery11">
                    @foreach ($photoSection->photos as $photo)
                        <a data-fancybox="gallery-{{ $sectionIndex }}" href="{{ $photo->url() }}"><img src="{{ $photo->url() }}"></a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="photogallerybox">
                <div class="gallery11">
                    <img src="{{ $car->coverUrl() }}">
                </div>
            </div>
        @endforelse
    </div>
</div>

@if ($car->crashTest || $car->testDrives->isNotEmpty())
    <div class="block_video">
        @if ($car->crashTest)
            <div class="crashtest_div">
                <a href="{{ $carPath }}/crash-test/">
                    <div class="crashtest_h">Краш-тест</div>
                    <div class="crashtest_a">
                        <div class="youtube" style="background-image: url('{{ $crashTestPreview }}');">
                            <div class="play"></div>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if ($car->testDrives->isNotEmpty())
            <div class="testdrive_div">
                <a href="{{ $carPath }}/test-drive/">
                    <div class="testdrive_h">Тест-драйв</div>
                    <div class="testdrive_a">
                        <div class="youtube" style="background-image: url('{{ $testDrivePreview }}');">
                            <div class="play"></div>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>
@endif

<script src="/job/CarsDo/js/fancybox_setup.js"></script>

@if ($car->reviews->isNotEmpty())
<div class="dop_photo"><a href="{{ $carPath }}/reviews/">ОТЗЫВЫ ВЛАДЕЛЬЦЕВ ({{ $car->reviews->count() }})</a></div>
@endif

@include('site.car.partials.specs-and-models', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
])
@endsection
