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

    $selectedGroupConfigurations = $configurations
        ->where('car_configuration_group_id', $selectedGroup->id)
        ->values();
    $selectedConfiguration = $selectedGroupConfigurations->first();

    $selectedGroupCategories = $selectedGroup->equipmentCategories
        ->sortBy([
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();

    $standardCategories = $selectedGroupCategories
        ->map(function ($category) {
            $items = $category->items
                ->sortBy([
                    ['import_index', 'asc'],
                    ['id', 'asc'],
                ])
                ->where('is_extension', false)
                ->filter(fn ($item) => filled($item->value))
                ->values();

            return (object) [
                'name' => $category->name,
                'items' => $items,
            ];
        })
        ->filter(fn ($category) => $category->items->isNotEmpty())
        ->values();

    $extensionItems = $selectedGroupCategories
        ->flatMap(function ($category) {
            return $category->items
                ->sortBy([
                    ['import_index', 'asc'],
                    ['id', 'asc'],
                ])
                ->where('is_extension', true)
                ->filter(fn ($item) => filled($item->value))
                ->values();
        })
        ->values();

    $formatPrice = static fn (?int $price): string => filled($price) ? number_format((int) $price, 0, ',', ' ') : '-';
    $formatValue = static fn ($value): string => filled($value) ? (string) $value : '-';

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

    $mainCars = $brand->cars
        ->where('is_soon', false)
        ->where('is_another_models', false)
        ->values();
    $newCars = $brand->cars->where('is_soon', true)->values();
    $otherCars = $brand->cars->where('is_another_models', true)->values();
    $currentYear = now()->year;
@endphp

@section('title', $car->name.' - '.$selectedGroup->name)

@section('content')
<div class="block1">
    <div class="hleb"><a href="/{{ $brand->slug }}/">Автомобили {{ $brand->name }}</a></div>

    <h1 style="padding-left:20px;">
        <a href="{{ $carPath }}/">{{ $car->name }}</a> › {{ $selectedGroup->name }}
    </h1>

    @if ($selectedConfiguration)
        <div class="characteristics_eq">
            {{ $formatValue($selectedConfiguration->engine_capacity) }} л
            ({{ $formatValue($selectedConfiguration->horsepower) }} л.с.)
            {{ $formatValue($selectedConfiguration->transmission) }}
            {{ $formatValue($selectedConfiguration->drive_type) }}
            {{ $formatValue($selectedConfiguration->engine_type) }}
        </div>
        <div class="EQ_price">от {{ $formatPrice($selectedConfiguration->price) }} руб.</div>
    @endif

    <div class="EQ_TM_a"><a href="#block_price3">Выбрать комплектацию</a></div>

    @if ($selectedConfiguration)
        <div class="EQ_chara">
            <div class="EQ_chara_1">
                <div class="EQ_chara_1_TITLE">Расход<br><div class="EQ_chara_span">в городе</div></div>
                <div class="EQ_chara_1_data">{{ $formatValue($selectedConfiguration->fuel_city) }}</div>
            </div>
            <div class="EQ_chara_2">
                <div class="EQ_chara_2_TITLE">Разгон<br><div class="EQ_chara_span">до 100, сек.</div></div>
                <div class="EQ_chara_2_data">{{ $formatValue($selectedConfiguration->acceleration) }}</div>
            </div>
            <div class="EQ_chara_3">
                <div class="EQ_chara_3_TITLE">Скорость<br><div class="EQ_chara_span">Max, км/ч</div></div>
                <div class="EQ_chara_3_data">{{ $formatValue($selectedConfiguration->speed) }}</div>
            </div>
        </div>
    @endif

    <div class="new_eq">
        <div class="new_eq2">
            @forelse ($standardCategories as $category)
                <div class="{{ $loop->index === 0 ? 'block_eq1' : ($loop->index === 1 ? 'block_eq2' : 'block_eq3') }}">
                    <ul class="komplektatsiya">
                        <li class="reto">{{ $category->name }}</li>
                        @foreach ($category->items as $item)
                            <li class="ok">{{ $item->value }}</li>
                        @endforeach
                    </ul>
                </div>
            @empty
                <div class="block_eq1">
                    <ul class="komplektatsiya">
                        <li class="reto">Оборудование</li>
                        <li class="ok">Данные по оборудованию пока не добавлены</li>
                    </ul>
                </div>
            @endforelse
        </div>

        <div class="price_kompl">
            <a href="{{ $carPath }}/">Цена {{ $car->name }}</a> <br>в этой комплектации:
            <br><span class="price_kompl_cena">{{ $selectedConfiguration ? $formatPrice($selectedConfiguration->price).' ₽' : 'не указана' }}</span>
        </div>

        @if ($extensionItems->isNotEmpty())
            <div class="block_eq4">
                <ul class="komplektatsiya">
                    <li class="reto">Дополнительное оборудование и опции</li>
                    @foreach ($extensionItems->take(10) as $item)
                        <li class="dop">
                            <span class="dop_obor">{{ $item->value }}</span>
                            <span class="dop_price">{{ filled($item->price) ? $formatPrice($item->price).' ₽' : '-' }}</span>
                        </li>
                    @endforeach
                    @if ($extensionItems->count() > 10)
                        <li class="dop"><a class="dop_a" href="#dop" onclick="view('dop'); return false">Все допы</a></li>
                        <div id="dop" style="display: none;">
                            @foreach ($extensionItems->slice(10) as $item)
                                <li class="dop">
                                    <span class="dop_obor">{{ $item->value }}</span>
                                    <span class="dop_price">{{ filled($item->price) ? $formatPrice($item->price).' ₽' : '-' }}</span>
                                </li>
                            @endforeach
                        </div>
                    @endif
                </ul>
            </div>
        @endif
    </div>
</div>

<div id="block_price3">
    <div class="tito-new"><h2>Модификации <br>{{ $car->name }}</h2></div>
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
    'dealerCitiesToggleId' => 'dealer-cities-equipment',
])

@include('site.car.partials.galery', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
    'galleryBlockId' => 'equipment-gallery',
])

<div style="padding:75px 12px 10px 20px;" class="tito-new"><h2>Комплектации и цены <br>{{ $car->name }}</h2></div>
<div class="price_new_margin">
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
<div id="block_photo_table_bottom">
    <div class="model_new_beg"><a href="/">CarsDo</a> › › <a href="/{{ $brand->slug }}/">Модельный ряд {{ $brand->name }} {{ $currentYear }}</a></div>

    @if ($mainCars->isNotEmpty())
        <div class="brand_model">
            @foreach ($mainCars as $mainCarIndex => $mainCar)
                @php $rowClass = $mainCarIndex % 2 === 0 ? 'brand_model_1' : 'brand_model_2'; @endphp
                <div class="{{ $rowClass }}">
                    <div class="brand_model_car"><a href="/{{ $brand->slug }}/{{ $mainCar->slug }}/">{{ $mainCar->name }}</a></div>
                    <div class="brand_model_price">{{ filled($mainCar->start_price) ? number_format((int) $mainCar->start_price, 0, ',', ' ').' ₽' : 'Цена не указана' }}</div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($newCars->isNotEmpty())
        <div class="brand_model_new_title"><div class="brand_model_new_title_1"><a href="/{{ $brand->slug }}/">Новые автомобили {{ $brand->name }} {{ $currentYear }}</a></div></div>
        <div class="brand_model_new">
            @foreach ($newCars as $newCarIndex => $newCar)
                @php $rowClass = $newCarIndex % 2 === 0 ? 'brand_model_1_new' : 'brand_model_2_new'; @endphp
                <div class="{{ $rowClass }}">
                    <div class="brand_model_car_new"><a href="/{{ $brand->slug }}/{{ $newCar->slug }}/">{{ $newCar->name }}</a></div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($otherCars->isNotEmpty())
        <div class="brand_model_new_title"><div class="brand_model_new_title_1">Другие модели</div></div>
        <div class="brand_model">
            @foreach ($otherCars as $otherCarIndex => $otherCar)
                @php $rowClass = $otherCarIndex % 2 === 0 ? 'brand_model_1' : 'brand_model_2'; @endphp
                <div class="{{ $rowClass }}">
                    <div class="brand_model_car"><a href="/{{ $brand->slug }}/{{ $otherCar->slug }}/">{{ $otherCar->name }}</a></div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="brand_model_new_title"><div class="brand_model_new_title_2"><a href="/new-cars-{{ $currentYear }}/">Все новые авто {{ $currentYear }}</a></div></div>
</div>
@endsection
