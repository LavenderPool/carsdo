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

    <h1 style="padding-left:20px;"><a href="{{ $carPath }}/">{{ $car->name }}</a> › {{ $selectedGroup->name }}</h1>

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
    $mainPhoto = $photos->first()?->photo_path ?: $car->coverUrl();
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
    $cityLinksMain = [
        'moscow' => 'Москва',
        'spb' => 'Санкт-Петербург',
        'astrakhan' => 'Астрахань',
        'volgograd' => 'Волгоград',
        'voronezh' => 'Воронеж',
        'ekaterinburg' => 'Екатеринбург',
        'kazan' => 'Казань',
        'krasnodar' => 'Краснодар',
        'krasnoyarsk' => 'Красноярск',
        'novgorod' => 'Нижний Новгород',
        'novosibirsk' => 'Новосибирск',
        'omsk' => 'Омск',
        'perm' => 'Пермь',
        'rostov' => 'Ростов',
        'samara' => 'Самара',
        'saratov' => 'Саратов',
        'sochi' => 'Сочи',
        'tver' => 'Тверь',
        'tyumen' => 'Тюмень',
        'ufa' => 'Уфа',
        'chelyabinsk' => 'Челябинск',
        'yaroslavl' => 'Ярославль',
    ];
    $cityLinksExtra = [
        'abakan' => 'Абакан',
        'almetyevsk' => 'Альметьевск',
        'armavir' => 'Армавир',
        'arkhangelsk' => 'Архангельск',
        'barnaul' => 'Барнаул',
        'belgorod' => 'Белгород',
        'berezniki' => 'Березники',
        'blagoveshchensk' => 'Благовещенск',
        'bratsk' => 'Братск',
        'bryansk' => 'Брянск',
        'velikiy-novgorod' => 'Великий Новгород',
        'vladivostok' => 'Владивосток',
        'vladimir' => 'Владимир',
        'vologda' => 'Вологда',
        'ivanovo' => 'Иваново',
        'izhevsk' => 'Ижевск',
        'irkutsk' => 'Иркутск',
        'yoshkar-ola' => 'Йошкар-Ола',
        'kaliningrad' => 'Калининград',
        'kaluga' => 'Калуга',
        'kemerovo' => 'Кемерово',
        'kirov' => 'Киров',
        'kopeysk' => 'Копейск',
        'kostroma' => 'Кострома',
        'kurgan' => 'Курган',
        'kursk' => 'Курск',
        'lipetsk' => 'Липецк',
        'magnitogorsk' => 'Магнитогорск',
        'makhachkala' => 'Махачкала',
        'miass' => 'Миасс',
        'mineralnyye-vody' => 'Минеральные Воды',
        'murmansk' => 'Мурманск',
        'naberezhnye-chelny' => 'Набережные Челны',
        'nizhnevartovsk' => 'Нижневартовск',
        'nizhniy-tagil' => 'Нижний Тагил',
        'novokuznetsk' => 'Новокузнецк',
        'novorossiysk' => 'Новороссийск',
        'orel' => 'Орел',
        'orenburg' => 'Оренбург',
        'orsk' => 'Орск',
        'penza' => 'Пенза',
        'petrozavodsk' => 'Петрозаводск',
        'pskov' => 'Псков',
        'pyatigorsk' => 'Пятигорск',
        'ryazan' => 'Рязань',
        'saransk' => 'Саранск',
        'simferopol' => 'Симферополь',
        'smolensk' => 'Смоленск',
        'stavropol' => 'Ставрополь',
        'stary-oskol' => 'Старый Оскол',
        'sterlitamak' => 'Стерлитамак',
        'surgut' => 'Сургут',
        'syktyvkar' => 'Сыктывкар',
        'tambov' => 'Тамбов',
        'tolyatty' => 'Тольятти',
        'tomsk' => 'Томск',
        'tula' => 'Тула',
        'ulyanovsk' => 'Ульяновск',
        'khabarovsk' => 'Хабаровск',
        'cheboksary' => 'Чебоксары',
        'cherepovets' => 'Череповец',
        'chita' => 'Чита',
        'shakhty' => 'Шахты',
        'engels' => 'Энгельс',
        'yuzhno-sakhalinsk' => 'Южно-Сахалинск',
    ];

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
<div class="block1">

    <h1 style="padding-left:20px;"><a href="/bmw/5-serii/">BMW 5 серии</a> › 530i xDrive Base</h1>
    <div class="characteristics_eq">2.0 л (249 л.с.) AT 4x4 бензин</div>
    <div class="EQ_price">от 5 650 000 руб.</div><div class="EQ_TM_a"><a href="#block_price3">Выбрать комплектацию</a></div>
    
    <div class="EQ_chara">
    <div class="EQ_chara_1"><div class="EQ_chara_1_TITLE">Расход<br><div class="EQ_chara_span">в городе</div></div><div class="EQ_chara_1_data">8.9</div></div>
    <div class="EQ_chara_2"><div class="EQ_chara_2_TITLE">Разгон<br><div class="EQ_chara_span">до 100, сек.</div></div><div class="EQ_chara_2_data">6.0</div></div>
    <div class="EQ_chara_3"><div class="EQ_chara_3_TITLE">Скорость<br><div class="EQ_chara_span">Max, км/ч</div></div><div class="EQ_chara_3_data">250</div></div>
    </div>
    
    
    
    <div class="new_eq"><div class="new_eq2">
    
    <div class="block_eq1"><ul class="komplektatsiya">
    <li class="reto">Безопасность и системы</li>
    <li class="ok">Знак аварийной остановки</li>
    <li class="ok">Сигнализация аварийного сближения при парковке (спереди и сзади)</li>
    <li class="ok">Функция интеллектуального экстренного вызова</li>
    <li class="ok">Индикатор давления в шинах</li>
    <li class="ok">Дистанционный запуск двигателя</li>
    </ul></div>
    
    
    </div><div class="block_eq2"><ul class="komplektatsiya">
    <li class="reto">Дизайн и внешнее оборудование</li>
    <li class="ok">Экстерьер "Белоснежный"</li>
    <li class="ok">17" легкосплавные диски</li>
    <li class="ok">Расширенный пакет зеркал заднего вида</li>
    <li class="ok">Светодиодные противотуманные фары</li>
    <li class="ok">Безопасные шины Runflat</li>
    </ul></div>
    

    
    </div><div class="block_eq3"><ul class="komplektatsiya">
    <li class="reto">Интерьер и оборудование салона</li>
    <li class="ok">Интерьер - Sensatec с перфорацией "Черный"</li>
    <li class="ok">Декоративные планки "Оксид серебра" темного матового цвета с акцентными вставками "Жемчужный Хром"</li>
    <li class="ok">Неослепляющее внутреннее зеркало заднего вида</li>
    <li class="ok">Спортивное кожаное рулевое колесо</li>
    <li class="ok">Пакет освещения</li>
    <li class="ok">Подогрев передних сидений</li>
    <li class="ok">Обогрев рулевого колеса</li>
    <li class="ok">Автоматический климат-контроль, 2 зоны</li>
    <li class="ok">BMW Teleservices</li>
    <li class="ok">Система громкой связи Hands-free, Bluetooth и USB</li>
    <li class="ok">Сервисы BMW ConnectedDrive</li>
    <li class="ok">Пакет Connected Professional</li>
    <li class="ok">BMW Live Cockpit Professional</li>
    </ul></div>
    

    
    </div><div class="price_kompl"><a href="/bmw/5-serii/">Цена BMW 5 серии</a> <br>в этой комплектации: <br><span class="price_kompl_cena">5 650 000 ₽</span></div>
    
    <div class="block_eq4"><ul class="komplektatsiya">
    <li class="reto">Дополнительное оборудование и опции</li>
    <li class="dop"><span class="dop_obor">Адаптивные светодиодные фары</span> <span class="dop_price">93 900 ₽</span></li>
    <li class="dop"><span class="dop_obor">Система управления дальним светом</span> <span class="dop_price">17 800 ₽</span></li>
    <li class="dop"><span class="dop_obor">Активная безопасность</span> <span class="dop_price">32 900 ₽</span></li>
    <li class="dop"><span class="dop_obor">Ассистент вождения Professional</span> <span class="dop_price">140 900 ₽</span></li>
    <li class="dop"><span class="dop_obor">Активный круиз-контроль c функцией Stop &amp; Go</span> <span class="dop_price">112 700 ₽</span></li>
    <li class="dop"><span class="dop_obor">Ассистент парковки</span> <span class="dop_price">70 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Фары M Shadow Line</span> <span class="dop_price">32 900 ₽</span></li>
    <li class="dop"><span class="dop_obor">Дизайн экстерьера M Shadow Line</span> <span class="dop_price">51 100 ₽</span></li>
    <li class="dop"><span class="dop_obor">Дизайн экстерьера M Shadow Line с расширенной отделкой</span> <span class="dop_price">28 200 ₽</span></li>
    <li class="dop"><span class="dop_obor">Люк электрорегулируемый с прозрачной крышкой</span> <span class="dop_price">117 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Солнцезащитные шторки</span> <span class="dop_price">56 400 ₽</span></li>
    
    <li class="dop"><a class="dop_a" href="#dop" onclick="view('dop'); return false">Все допы</a></li>
    
    <div id="dop" style="display: none;">
    <li class="dop"><span class="dop_obor">Велюровые коврики</span> <span class="dop_price">14 100 ₽</span></li>
    <li class="dop"><span class="dop_obor">Электрорегулировка передних сидений, с функцией памяти для сиденья водителя</span> <span class="dop_price">112 700 ₽</span></li>
    <li class="dop"><span class="dop_obor">Спортивные сиденья для водителя и переднего пассажира</span> <span class="dop_price">61 100 ₽</span></li>
    <li class="dop"><span class="dop_obor">Электрорегулируемая поддержка поясничного отдела спины</span> <span class="dop_price">25 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Ремни безопасности М</span> <span class="dop_price">28 200 ₽</span></li>
    <li class="dop"><span class="dop_obor">Подогрев передних и задних сидений</span> <span class="dop_price">37 600 ₽</span></li>
    <li class="dop"><span class="dop_obor">Пакет Ambient Air (ионизация и ароматизация воздуха в салоне)</span> <span class="dop_price">32 900 ₽</span></li>
    <li class="dop"><span class="dop_obor">Керамическая отделка органов управления</span> <span class="dop_price">61 100 ₽</span></li>
    <li class="dop"><span class="dop_obor">Кожаное рулевое колесо M</span> <span class="dop_price">18 800 ₽</span></li>
    <li class="dop"><span class="dop_obor">Обивка потолка салона M "Антрацит"</span> <span class="dop_price">37 600 ₽</span></li>
    <li class="dop"><span class="dop_obor">Проекционный дисплей</span> <span class="dop_price">117 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Аудиосистема Hi-Fi</span> <span class="dop_price">47 000 ₽</span></li>
    <li class="dop"><span class="dop_obor">Аудиосистема Harman/Kardon Surround Sound</span> <span class="dop_price">103 300 ₽</span></li>
    <li class="dop"><span class="dop_obor">Телефония с возможностью беспроводной зарядки</span> <span class="dop_price">56 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Охранная сигнализация</span> <span class="dop_price">47 000 ₽</span></li>
    <li class="dop"><span class="dop_obor">Автоматический привод багажника</span> <span class="dop_price">56 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Встроенный пульт дистанционного управления</span> <span class="dop_price">24 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Комфортный доступ</span> <span class="dop_price">84 500 ₽</span></li>
    <li class="dop"><span class="dop_obor">Автодоводчик дверей</span> <span class="dop_price">61 100 ₽</span></li>
    <li class="dop"><span class="dop_obor">Пакет для курящих</span> <span class="dop_price">4 700 ₽</span></li>
    <li class="dop"><span class="dop_obor">Система сквозной погрузки</span> <span class="dop_price">42 300 ₽</span></li>
    <li class="dop"><span class="dop_obor">Тормозная система M Sport с суппортами синего/красного цвета</span> <span class="dop_price">65 800 ₽</span></li>
    <li class="dop"><span class="dop_obor">Спортивная автоматическая коробка передач Steptronic</span> <span class="dop_price">23 500 ₽</span></li>
    <li class="dop"><span class="dop_obor">Интегральное активное рулевое управление</span> <span class="dop_price">117 400 ₽</span></li>
    <li class="dop"><span class="dop_obor">Подвеска M Sport</span> <span class="dop_price">42 300 ₽</span></li>
    <li class="dop"><span class="dop_obor">Аварийное запасное колесо</span> <span class="dop_price">32 900 ₽</span></li>
    </div>
    </ul></div>
    
    </div></div><div id="block_price3"><div class="tito-new"><h2>Модификации <br>BMW 5 серии</h2></div>
    <p><span style="font-weight:bold;" class="complete_text">Выберите комплектацию, далее модификацию (двигатель и коробка передач)</span>, чтобы посмотреть: безопасность и системы автомобиля + дизайн (внешнее оборудование) + интерьер (оборудование салона) + дополнительные платные опции (при наличии) к комплектации от завода изготовителя.</p>
    <ul id="complete">
    <li><a href="/bmw/5-serii/equipment-1/"><span class="clt1">520i Base</span><span class="clt2">2.0 л (184 л.с.) AT 2x4 бензин</span><span class="clt3">5 120 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-2/"><span class="clt1">520d Base</span><span class="clt2">2.0 л (190 л.с.) AT 2x4 дизель</span><span class="clt3">5 230 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-3/"><span class="clt1">520d xDrive Base</span><span class="clt2">2.0 л (190 л.с.) AT 4x4 дизель</span><span class="clt3">5 380 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-4/"><span class="clt1">520i Business</span><span class="clt2">2.0 л (184 л.с.) AT 2x4 бензин</span><span class="clt3">5 420 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-5/"><span class="clt1">520d Executive</span><span class="clt2">2.0 л (190 л.с.) AT 2x4 дизель</span><span class="clt3">5 620 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-6/"><span class="clt1">530i xDrive Base</span><span class="clt2">2.0 л (249 л.с.) AT 4x4 бензин</span><span class="clt3">5 650 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-7/"><span class="clt1">520d xDrive Business</span><span class="clt2">2.0 л (190 л.с.) AT 4x4 дизель</span><span class="clt3">5 680 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-8/"><span class="clt1">520d xDrive M Sport Pure</span><span class="clt2">2.0 л (190 л.с.) AT 4x4 дизель</span><span class="clt3">5 950 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-9/"><span class="clt1">530d xDrive Base</span><span class="clt2">3.0 л (249 л.с.) AT 4x4 дизель</span><span class="clt3">6 120 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-10/"><span class="clt1">530i xDrive M Sport Plus</span><span class="clt2">2.0 л (249 л.с.) AT 4x4 бензин</span><span class="clt3">6 230 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-11/"><span class="clt1">540i xDrive Base</span><span class="clt2">3.0 л (340 л.с.) AT 4x4 бензин</span><span class="clt3">6 700 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-12/"><span class="clt1">530d xDrive M Sport Plus</span><span class="clt2">3.0 л (249 л.с.) AT 4x4 дизель</span><span class="clt3">6 870 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-13/"><span class="clt1">530d xDrive M Sport Pro</span><span class="clt2">3.0 л (249 л.с.) AT 4x4 дизель</span><span class="clt3">7 250 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-14/"><span class="clt1">540i xDrive M Sport Pro</span><span class="clt2">3.0 л (340 л.с.) AT 4x4 бензин</span><span class="clt3">7 880 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-15/"><span class="clt1">M550i xDrive Base</span><span class="clt2">4.4 л (530 л.с.) AT 4x4 бензин</span><span class="clt3">9 050 000 руб.</span></a></li>
    <li><a href="/bmw/5-serii/equipment-16/"><span class="clt1">M550i xDrive M Special</span><span class="clt2">4.4 л (530 л.с.) AT 4x4 бензин</span><span class="clt3">10 260 000 руб.</span></a></li>
    </ul></div><div id="block_city">
    
    <div class="title_city"><h3>Официальные дилеры</h3></div>
    <p style="color:#fff;">Где купить BMW 5 серии в России.</p>
    
    <div class="kupmos">
    
    <a href="/bmw/5-serii/moscow/">Москва</a>
    <a href="/bmw/5-serii/spb/">Санкт-Петербург</a>
    <a href="/bmw/5-serii/belgorod/">Белгород</a>
    <a href="/bmw/5-serii/volgograd/">Волгоград</a>
    <a href="/bmw/5-serii/voronezh/">Воронеж</a>
    <a href="/bmw/5-serii/ekaterinburg/">Екатеринбург</a>
    <a href="/bmw/5-serii/izhevsk/">Ижевск</a>
    <a href="/bmw/5-serii/kazan/">Казань</a>
    <a href="/bmw/5-serii/krasnodar/">Краснодар</a>
    <a href="/bmw/5-serii/krasnoyarsk/">Красноярск</a>
    <a href="/bmw/5-serii/novgorod/">Нижний Новгород</a>
    <a href="/bmw/5-serii/novosibirsk/">Новосибирск</a>
    <a href="/bmw/5-serii/omsk/">Омск</a>
    <a href="/bmw/5-serii/orenburg/">Оренбург</a>
    <a href="/bmw/5-serii/perm/">Пермь</a>
    <a href="/bmw/5-serii/rostov/">Ростов</a>
    <a href="/bmw/5-serii/samara/">Самара</a>
    <a href="/bmw/5-serii/saratov/">Саратов</a>
    <a href="/bmw/5-serii/surgut/">Сургут</a>
    <a href="/bmw/5-serii/tolyatty/">Тольятти</a>
    <a href="/bmw/5-serii/tyumen/">Тюмень</a>
    <a href="/bmw/5-serii/ufa/">Уфа</a>
    <a href="/bmw/5-serii/chelyabinsk/">Челябинск</a>
    
    </div></div>
    <div style="padding:75px 12px 10px 20px;" class="tito-new"><h2>Комплектации и цены <br>BMW 5 серии</h2></div><div class="price_new_margin">
    
    <div id="price_new">
    <div class="price_car_0"><div class="pc_price">Цена</div><div class="pc_1">Двигатель</div><div class="pc_2">Коробка</div><div class="pc_3">Привод</div><div class="pc_4">Расход</div><div class="pc_5">Разгон</div><div class="pc_6">Скорость</div></div>
    
    
    <div class="price_car_1">
            <div class="pc_name"><a href="/bmw/5-serii/equipment-1/">520i Base</a></div>
        <div class="price_modific">
            <div class="pc_price">5 120 000 <span class="des">руб.</span></div>
            <div class="pc_1">бензин <span class="motor">2.0 л.</span> | 184 <span class="des">л.с.</span></div>
            <div class="pc_2">AT</div>
            <div class="pc_3">Задний</div>
            <div class="pc_4">8.5 | 5.8 | 6.8</div>
            <div class="pc_5">7.8</div>
            <div class="pc_6">235 <span class="des">км/ч</span></div>
        </div>
    </div>
    
    
    
    
    
    <div class="price_car_2">
            <div class="pc_name"><a href="/bmw/5-serii/equipment-16/">M550i xDrive M Special</a></div>
        <div class="price_modific">
            <div class="pc_price">10 260 000 <span class="des">руб.</span></div>
            <div class="pc_1">бензин <span class="motor">4.4 л.</span> | 530 <span class="des">л.с.</span></div>
            <div class="pc_2">AT</div>
            <div class="pc_3"><span class="x4">4x4</span> Полный</div>
            <div class="pc_4">14.9 | 8.3 | 10.7</div>
            <div class="pc_5">3.8</div>
            <div class="pc_6">250 <span class="des">км/ч</span></div>
        </div>
    </div>
    
    </div>
    
    <div class="mini_price"><a href="/bmw/5-serii/">ВСЕ КОМПЛЕКТАЦИИ И ЦЕНЫ</a></div></div>
    <div id="block_price4"><div><div class="photo_title">Новый BMW 5 серии. Скоро в продаже</div></div><div><img class="preview_photo" src="/job/CarsDo/photo-gallery/bmw/5-serii-1-25.jpg"></div><div class="preview_photo_mini"><img src="/job/CarsDo/photo-gallery/bmw/5-serii-1-25.jpg"><img src="/job/CarsDo/photo-gallery/bmw/5-serii-1-26.jpg"><img src="/job/CarsDo/photo-gallery/bmw/5-serii-1-29.jpg"><img src="/job/CarsDo/photo-gallery/bmw/5-serii-1-13.jpg"><img src="/job/CarsDo/photo-gallery/bmw/5-serii-1-14.jpg"><img src="/job/CarsDo/photo-gallery/bmw/5-serii-1-15.jpg"></div><div class="dop_photo"><a href="/bmw/5-serii/photo/">ВСЕ ФОТО</a></div><div><script type="text/javascript">$('.preview_photo_mini').delegate('img','click', function(){$('.preview_photo').attr('src',$(this).attr('src').replace('thumb','large'));;});</script></div></div>
    
    <div class="block_video"><div class="crashtest_div"><a href="/bmw/5-serii/crash-test/"><div class="crashtest_h">Краш-тест</div><div class="crashtest_a"><img alt="Краш-тест BMW 5 серии" src="/job/CarsDo/photo/crash-test/bmw-5-serii.jpg"></div></a></div><div class="testdrive_div"><a href="/bmw/5-serii/test-drive/"><div class="testdrive_h">Тест-драйв</div><div class="testdrive_a"><img alt="Тест-драйв BMW 5 серии" src="/job/CarsDo/photo/test-drive/bmw-5-serii.jpg"></div></a></div></div>
    <div id="page_price_5"></div><div id="block_moscow_table_bottom"><div class="model_new_beg"><a href="/">CarsDo</a> › › <a href="/bmw/">Модельный ряд BMW 2026</a></div>
    
    
    
    <div class="brand_model">
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/2-coupe/">2 серии</a></div>
    <div class="brand_model_price">3 970 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/2-serii-gran-coupe/">2 серии Gran Coupe</a></div>
    <div class="brand_model_price">3 380 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/3-serii/">3 серии</a></div>
    <div class="brand_model_price">3 930 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/4-coupe/">4 серии</a></div>
    <div class="brand_model_price">4 450 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/4-gran-coupe/">4 серии Гран Купе</a></div>
    <div class="brand_model_price">4 550 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/4-cabriolet/">4 серии Кабриолет</a></div>
    <div class="brand_model_price">5 000 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/5-serii/">5 серии</a></div>
    <div class="brand_model_price">5 120 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/6-serii-gt/">6 серии GT</a></div>
    <div class="brand_model_price">5 990 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/7-serii/">7 серии</a></div>
    <div class="brand_model_price">8 630 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/8-serii/">8 серии</a></div>
    <div class="brand_model_price">9 780 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/8-serii-cabriolet/">8 серии Кабриолет</a></div>
    <div class="brand_model_price">10 920 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/8-serii-gran-coupe/">8 серии Gran Coupe</a></div>
    <div class="brand_model_price">9 450 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/ix/">iX</a></div>
    <div class="brand_model_price">9 580 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/m3-sedan/">M3</a></div>
    <div class="brand_model_price">8 900 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/m4/">M4</a></div>
    <div class="brand_model_price">9 000 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/m5/">M5</a></div>
    <div class="brand_model_price">11 450 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/m8/">M8</a></div>
    <div class="brand_model_price">14 570 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/m8-cabriolet/">M8 Кабриолет</a></div>
    <div class="brand_model_price">15 550 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/m8-gran-coupe/">M8 Gran Coupe</a></div>
    <div class="brand_model_price">13 980 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/x1/">X1</a></div>
    <div class="brand_model_price">3 280 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/x2/">X2</a></div>
    <div class="brand_model_price">3 520 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/x3/">X3</a></div>
    <div class="brand_model_price">5 320 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/x3-m/">X3 M</a></div>
    <div class="brand_model_price">7 370 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/x4/">X4</a></div>
    <div class="brand_model_price">5 700 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/x4-m/">X4 M</a></div>
    <div class="brand_model_price">7 510 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/x5/">X5</a></div>
    <div class="brand_model_price">7 190 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/x5-m/">X5 M</a></div>
    <div class="brand_model_price">13 820 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/x6/">X6</a></div>
    <div class="brand_model_price">8 400 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/x6-m/">X6 M</a></div>
    <div class="brand_model_price">14 150 000 ₽</div>
    </div>
    
    
    <div class="brand_model_2">
    <div class="brand_model_car"><a href="/bmw/x7/">X7</a></div>
    <div class="brand_model_price">9 350 000 ₽</div>
    </div>
    
    
    <div class="brand_model_1">
    <div class="brand_model_car"><a href="/bmw/z4/">Z4</a></div>
    <div class="brand_model_price">5 110 000 ₽</div>
    </div>
    
    </div>
    
    
    
    
    
    <div class="brand_model_new_title"><div class="brand_model_new_title_1"><a href="/bmw/">Новые автомобили BMW 2026</a></div></div>
    
    
    <div class="brand_model_new">
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/3-serii-touring/">3 серии Touring</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/m3-touring/">M3 Touring</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/1-serii/">1 серии</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/2-serii-active-tourer/">2 серии Active Tourer</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/5-universal/">5 серии Туринг</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/i3/">i3</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/i4/">i4</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/i5/">i5</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/i5/">i5 Touring</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/i5-touring/">i7</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/ix1/">iX1</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/ix2/">iX2</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/ix3/">iX3</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/m2/">M2</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/m4-cabriolet/">M4 Кабриолет</a></div>
    </div>
    
    
    <div class="brand_model_2_new">
    <div class="brand_model_car_new"><a href="/bmw/m5-touring/">M5 Touring</a></div>
    </div>
    
    
    <div class="brand_model_1_new">
    <div class="brand_model_car_new"><a href="/bmw/xm/">XM</a></div>
    </div>
    
    </div>
    
    
    <div class="brand_model_new_title"><div class="brand_model_new_title_2"><a href="/new-cars-2026/">Все новые авто 2026</a></div></div></div></div>
@endsection
