@php
    $carPath = isset($carPath) && is_string($carPath) && $carPath !== ''
        ? $carPath
        : '/'.$brand->slug.'/'.$car->slug;

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
        ->values()
        ->groupBy('car_configuration_group_id');
    $equipmentUrl = static function ($configuration) use ($carPath): ?string {
        if (! $configuration || ! filled($configuration->local_id)) {
            return null;
        }

        return $carPath.'/equipment-'.$configuration->local_id.'/';
    };
    $primaryConfigurationForGroup = static function (int $groupId) use ($configurations) {
        $groupConfigurations = $configurations->get($groupId, collect())->values();

        return $groupConfigurations->first(fn ($configuration) => filled($configuration->local_id))
            ?? $groupConfigurations->first();
    };

    $mainCars = $brand->cars
        ->where('is_soon', false)
        ->where('is_another_models', false)
        ->values();
    $newCars = $brand->cars->where('is_soon', true)->values();
    $otherCars = $brand->cars->where('is_another_models', true)->values();
    $currentYear = now()->year;

    $formatPrice = static function (?int $price): string {
        return filled($price) ? number_format((int) $price, 0, ',', ' ').' ₽' : 'Цена не указана';
    };

    $formatNumber = static function (mixed $value): string {
        return filled($value) ? (string) $value : '-';
    };
@endphp

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
                $rowClass = $groupIndex % 2 === 0 ? 'price_car_1' : 'price_car_2';
                $groupConfigurations = $configurations->get($group->id, collect());
                $groupUrl = $equipmentUrl($primaryConfigurationForGroup($group->id));
            @endphp
            <div class="{{ $rowClass }}">
                <div class="pc_name">
                    @if ($groupUrl)
                        <a href="{{ $groupUrl }}">{{ $group->name }}</a>
                    @else
                        {{ $group->name }}
                    @endif
                </div>
                @foreach ($groupConfigurations as $configuration)
                    <div class="price_modific">
                        <div class="pc_price">{{ filled($configuration->price) ? number_format((int) $configuration->price, 0, ',', ' ') : '-' }} <span class="des">руб.</span></div>
                        <div class="pc_1">
                            {{ $formatNumber($configuration->engine_type) }}
                            @if (filled($configuration->engine_capacity))
                                <span class="motor">{{ rtrim(rtrim((string) $configuration->engine_capacity, '0'), '.') }} л.</span>
                            @endif
                            @if (filled($configuration->horsepower))
                                | {{ $formatNumber($configuration->horsepower) }} <span class="des">л.с.</span>
                            @endif
                        </div>
                        <div class="pc_2">{{ $formatNumber($configuration->transmission) }}</div>
                        <div class="pc_3">{{ $formatNumber($configuration->drive_type) }}</div>
                        <div class="pc_4">{{ $formatNumber($configuration->fuel_city) }} | {{ $formatNumber($configuration->fuel_highway) }} | {{ $formatNumber($configuration->fuel_combined) }}</div>
                        <div class="pc_5">{{ $formatNumber($configuration->acceleration) }}</div>
                        <div class="pc_6">{{ $formatNumber($configuration->speed) }} <span class="des">км/ч</span></div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="price_car_1">
                <div class="pc_name">{{ $car->name }}</div>
                <div class="price_modific">
                    <div class="pc_price">Данные о комплектациях пока не добавлены</div>
                </div>
            </div>
        @endforelse
    </div>
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
                    <div class="brand_model_price">{{ $formatPrice($mainCar->start_price) }}</div>
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
