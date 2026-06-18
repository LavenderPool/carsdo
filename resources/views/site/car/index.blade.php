@extends('layouts.site')

@php
    $configurations = $car->configurations
        ->sortBy([
            ['car_configuration_group_id', 'asc'],
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();
    $configurationGroups = $car->configurationGroups->sortBy('order')->values();
    $photos = $car->photos
        ->concat($car->photoGroups->flatMap->photos)
        ->filter(fn ($photo) => filled($photo->photo_path))
        ->unique(fn ($photo) => $photo->id)
        ->values();
    $mainPhoto = $photos->first()?->url() ?: $car->coverUrl();
    $now = now();
    $currentYear = $now->year;
    $carPath = '/'.$brand->slug.'/'.$car->slug;
    $mainCars = $brand->cars
        ->where('is_another_models', false)
        ->where('is_soon', false)
        ->sortBy('name')
        ->values();
    $newCars = $brand->cars
        ->where('is_soon', true)
        ->sortBy('name')
        ->values();
    $minPrice = $configurations->whereNotNull('price')->min('price') ?? $car->start_price;
    $maxPrice = $configurations->whereNotNull('price')->max('price') ?? $car->end_price ?? $car->start_price;
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

@section('title', 'Модельный ряд ' . $brand->name . ' ' . $car->name)

@section('content')
<div class="block_price">
    <div id="block_price1">
        <div class="block_PN_1">
            <div class="data_price"><div class="dp1">Официальные данные от {{ $now->format('d.m.Y') }}</div></div>
            <div class="block_PN_H1"><h1>{{ $pageH1 ?? $car->name }}</h1></div>

            <div class="block_PN_1_a">
                <div class="block_PN_1_b">
                    <div class="PN_1">
                        <div class="PN_1_div1">Цена</div>
                        <div class="PN_1_div2"><a href="#price_new">{{ $priceRangeText }}</a></div>
                    </div>

                    <div class="PN_2">
                        <div class="PN_2_div1">Комплектации</div>
                        <div class="PN_2_div2"><a href="#price_new">{{ $configurationGroups->count() }}</a></div>
                    </div>

                    <div class="PN_3">
                        <div class="PN_3_div1">Модификации</div>
                        <div class="PN_3_div2"><a href="#block_price3">{{ $configurations->count() }}</a></div>
                    </div>

                    <div class="PN_4">
                        <div class="PN_4_div1">Дизайн новой модели</div>
                        <div class="PN_4_div2"><a href="{{ $carPath }}/photo/">Фото</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="block_PN_2">
            <div class="preview">
                <a href="{{ $carPath }}/photo/">
                    @forelse ($photos->take(4) as $photo)
                                <img src="{{ $photo->url() }}">
                    @empty
                        <img src="{{ $car->coverUrl() }}">
                    @endforelse
                </a>
            </div>
        </div>
    </div>

    <div class="price_H2"><h2>{{ $brand->name }} {{ $car->name }} › Цены и комплектации</h2></div>

    <div class="price_new_text">
        В России цена {{ $brand->name }} {{ $car->name }} в новом кузове составляет {{ $priceRangeText }} рублей,
        автомобиль продается в {{ $configurationGroups->count() }} комплектациях
        (официальный сайт
        @if (filled($car->official_site))
            <a target="_blank" rel="noopener noreferrer" href="{{ $car->official_site }}">{{ $brand->name }}</a>
        @else
            {{ $brand->name }}
        @endif
        ).
        Стоимость нового автомобиля в {{ $currentYear }} году у официального дилера во всех городах РФ одинаковая и зависит
        от выбранной комплектации и дополнительных опций. <a target="_self" href="#block_city">Найти дилера</a>.
    </div>

    <div id="price_new">
        <div class="price_car_0">
            <div class="pc_price">Цена</div>
            <div class="pc_1">Двигатель</div>
            <div class="pc_2">Коробка</div>
            <div class="pc_3">Привод</div>
            <div class="pc_4">Расход</div>
            <div class="pc_5">Разгон</div>
            <div class="pc_6">Скорость</div>
        </div>

        @forelse ($configurationGroups as $groupIndex => $group)
            @php
                $groupConfigurations = $configurations->where('car_configuration_group_id', $group->id)->values();
            @endphp
            <div class="{{ $loop->odd ? 'price_car_1' : 'price_car_2' }}">
                <div class="pc_name"><a href="{{ $carPath }}/equipment-{{ $groupIndex + 1 }}/">{{ $group->name }}</a></div>
                @forelse ($groupConfigurations as $configuration)
                    <div class="price_modific">
                        <div class="pc_price">{{ $formatPrice($configuration->price) }} <span class="des">руб.</span></div>
                        <div class="pc_1">
                            {{ $configuration->engine_type ?: '-' }}
                            <span class="motor">{{ $configuration->engine_capacity ?: '-' }} л.</span>
                            | {{ $configuration->horsepower ?: '-' }} <span class="des">л.с.</span>
                        </div>
                        <div class="pc_2">{{ $configuration->transmission ?: '-' }}</div>
                        <div class="pc_3">{{ $configuration->drive_type ?: '-' }}</div>
                        <div class="pc_4">{{ $configuration->fuel_city ?: '-' }} | {{ $configuration->fuel_highway ?: '-' }} | {{ $configuration->fuel_combined ?: '-' }}</div>
                        <div class="pc_5">{{ $configuration->acceleration ?: '-' }}</div>
                        <div class="pc_6">{{ $configuration->speed ?: '-' }} <span class="des">км/ч</span></div>
                    </div>
                @empty
                    <div class="price_modific">
                        <div class="pc_price">Данные по модификациям скоро появятся</div>
                    </div>
                @endforelse
            </div>
        @empty
            <div class="price_car_1">
                <div class="pc_name">Комплектации пока не добавлены</div>
            </div>
        @endforelse
    </div>

    <div class="dop_photo"><a href="{{ $carPath }}/reviews/">ОТЗЫВЫ ВЛАДЕЛЬЦЕВ ({{ $car->reviews->count() }})</a></div>
    <div style="width: 100%; margin:10px 0 15px;"></div>

    <div id="block_price3">
        <div class="tito-new"><h2>Модификации <br>{{ $brand->name }} {{ $car->name }}</h2></div>
        <p><span style="font-weight:bold;" class="complete_text">Выберите комплектацию, далее модификацию (двигатель и коробка передач)</span>, чтобы посмотреть: безопасность и системы автомобиля + дизайн (внешнее оборудование) + интерьер (оборудование салона) + дополнительные платные опции (при наличии) к комплектации от завода изготовителя.</p>
        <ul id="complete">
            @forelse ($configurationGroups as $groupIndex => $group)
                @php
                    $groupConfigurations = $configurations->where('car_configuration_group_id', $group->id)->values();
                @endphp
                @forelse ($groupConfigurations as $configuration)
                    <li>
                        <a href="{{ $carPath }}/equipment-{{ $groupIndex + 1 }}/">
                            <span class="clt1">{{ $group->name }}</span>
                            <span class="clt2">
                                {{ $configuration->engine_capacity ?: '-' }} л ({{ $configuration->horsepower ?: '-' }} л.с.)
                                {{ $configuration->transmission ?: '-' }} {{ $configuration->drive_type ?: '-' }} {{ $configuration->engine_type ?: '-' }}
                            </span>
                            <span class="clt3">{{ $formatPrice($configuration->price) }} руб.</span>
                        </a>
                    </li>
                @empty
                    <li>
                        <span class="clt1">{{ $group->name }}</span>
                        <span class="clt2">Модификации пока не заполнены</span>
                        <span class="clt3">-</span>
                    </li>
                @endforelse
            @empty
                <li>
                    <span class="clt1">Нет данных</span>
                    <span class="clt2">Комплектации и модификации пока не добавлены</span>
                    <span class="clt3">-</span>
                </li>
            @endforelse
        </ul>
    </div>

    @include('site.car.partials.dealer-cities', [
        'brand' => $brand,
        'car' => $car,
        'carPath' => $carPath,
        'dealerCitiesToggleId' => 'dealer-cities-index',
    ])

    @include('site.car.partials.galery', [
        'brand' => $brand,
        'car' => $car,
        'carPath' => $carPath,
        'photos' => $photos,
        'galleryBlockId' => 'car-gallery',
    ])
</div>

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

<div id="page_price_5"></div>
<div id="block_price5">
    <div class="model_new_beg"><a href="/">CarsDo</a> › › <a href="/{{ $brand->slug }}/">Модельный ряд {{ $brand->name }} {{ $currentYear }}</a></div>

    <div class="brand_model">
        @forelse ($mainCars as $brandCar)
            <div class="{{ $loop->odd ? 'brand_model_1' : 'brand_model_2' }}">
                <div class="brand_model_car"><a href="/{{ $brand->slug }}/{{ $brandCar->slug }}/">{{ $brandCar->name }}</a></div>
                <div class="brand_model_price">{{ $formatPrice($brandCar->start_price) }} ₽</div>
            </div>
        @empty
            <div class="brand_model_1">
                <div class="brand_model_car">Модели пока не добавлены</div>
            </div>
        @endforelse
    </div>

    @if ($newCars->isNotEmpty())
        <div class="brand_model_new_title"><div class="brand_model_new_title_1"><a href="/{{ $brand->slug }}/">Новые автомобили {{ $brand->name }} {{ $currentYear }}</a></div></div>
        <div class="brand_model_new">
            @foreach ($newCars as $newCar)
                <div class="{{ $loop->odd ? 'brand_model_1_new' : 'brand_model_2_new' }}">
                    <div class="brand_model_car_new"><a href="/{{ $brand->slug }}/{{ $newCar->slug }}/">{{ $newCar->name }}</a></div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="brand_model_new_title"><div class="brand_model_new_title_2"><a href="/new-cars-{{ $currentYear }}/">Все новые авто {{ $currentYear }}</a></div></div>
</div>
@endsection
