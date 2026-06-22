@extends('layouts.site')

@php
    $carPath = '/'.$brand->slug.'/'.$car->slug;
    $photos = $car->photos
        ->concat($car->photoGroups->flatMap->photos)
        ->filter(fn ($photo) => filled($photo->photo_path))
        ->unique(fn ($photo) => $photo->id)
        ->values();
    $configurations = $car->configurations
        ->sortBy([
            ['car_configuration_group_id', 'asc'],
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();
    $minPrice = $configurations->whereNotNull('price')->min('price') ?? $car->start_price;
    $maxPrice = $configurations->whereNotNull('price')->max('price') ?? $car->end_price ?? $car->start_price;
    $currentYear = now()->year;
    $cityInPrepositional = $city->nameInPrepositionalCase();
    $formatPrice = static fn (?int $price): string => filled($price) ? number_format((int) $price, 0, ',', ' ') : 'не объявлена';
    $priceRangeText = filled($minPrice) && filled($maxPrice)
        ? ($minPrice === $maxPrice ? $formatPrice((int) $minPrice) : $formatPrice((int) $minPrice).' - '.$formatPrice((int) $maxPrice))
        : 'не объявлена';

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

@section('title', ''.$car->name.'- Официальные дилеры')

@section('content')
<div class="block1">
    <div class="block_moscow">
        <h1>
            @if (filled($pageH1 ?? null))
                {{ $pageH1 }}
            @else
                <a href="{{ $carPath }}/">{{ $car->name }}</a> › Официальные дилеры ({{ $city->name }})
            @endif
        </h1>

        <p>
            Где купить новый {{ $car->name }} у официального дилера в {{ $cityInPrepositional }}. Ознакомиться с актуальными ценами,
            скидками и другими специальными предложениями на {{ $currentYear }} год можно по ссылке "Смотреть цены" на официальном сайте автосалона.
            Также вы можете посмотреть <a href="{{ $carPath }}/">официальные комплектации и цены</a> и <a href="{{ $carPath }}/photo/">фото {{ $car->name }}</a> новой модели.
        </p>
    </div>

    <h2>Где купить {{ $car->name }} в {{ $cityInPrepositional }}</h2>
    <div class="start_salon">
        <ul class="salon_new">
            @foreach ($cityDealers as $cityDealer)
                <li class="salon_normal">
                    <div class="salon_new_div01">
                        <div class="salon_new_div1">{{ $cityDealer->dealer?->name ?? 'Дилер' }}</div>
                        <div class="salon_new_div2">{{ $cityDealer->is_official ? 'Официальный дилер' : 'Дилер' }}</div>
                        @if (filled($cityDealer->address))
                            <div class="salon_new_div3">{{ $cityDealer->address }}</div>
                        @endif
                        @if (filled($cityDealer->phone))
                            <div class="salon_new_div4">{{ $cityDealer->phone }}</div>
                        @endif
                    </div>

                    @if (filled($cityDealer->website))
                        <div class="salon_new_div02">
                            <div class="salon_new_div5"></div>
                            <div class="salon_new_div6">
                                <a class="salon_new_a" href="{{ $cityDealer->website }}" target="_blank" rel="nofollow noopener noreferrer">Смотреть цены</a>
                            </div>
                        </div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

@include('site.car.partials.dealer-cities', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
    'currentCitySlug' => $city->slug,
    'dealerCitiesToggleId' => 'dealer-cities-city-page',
])

@if ($car->reviews->isNotEmpty())
<div class="dop_photo"><a href="{{ $carPath }}/reviews/">ОТЗЫВЫ ВЛАДЕЛЬЦЕВ ({{ $car->reviews->count() }})</a></div>
@endif
@include('site.car.partials.galery', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
    'photos' => $photos,
    'galleryBlockId' => 'car-gallery-dealer',
])

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

@include('site.car.partials.specs-and-models', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
])
@endsection
