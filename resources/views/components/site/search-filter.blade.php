@props([
    'query' => '',
    'filters' => [],
    'filterOptions' => [],
    'rangeBounds' => [],
    'brandOptions' => collect(),
    'action' => null,
    'resetUrl' => null,
    'formId' => 'searchFiltersForm',
    'title' => 'Фильтры',
    'subtitle' => 'Подберите параметры и сразу сравните подходящие автомобили.',
    'class' => '',
])

@php
    $filters = is_array($filters ?? null) ? $filters : [];
    $filterOptions = is_array($filterOptions ?? null) ? $filterOptions : [];
    $rangeBounds = is_array($rangeBounds ?? null) ? $rangeBounds : [];
    $brandOptions = $brandOptions ?? collect();
    $query = is_string($query ?? null) ? $query : '';
    $action = $action ?: route('search');
    $resetUrl = $resetUrl ?: route('search');
    $selectedBrand = $filters['brand'] ?? null;
    $priceBounds = $rangeBounds['price'] ?? ['min' => 1000000, 'max' => 10000000, 'step' => 10000];
    $engineBounds = $rangeBounds['engine_capacity'] ?? ['min' => 1.0, 'max' => 4.0, 'step' => 0.1];
    $fuelBounds = $rangeBounds['fuel_combined'] ?? ['min' => 4.0, 'max' => 20.0, 'step' => 0.1];
    $powerBounds = $rangeBounds['horsepower'] ?? ['min' => 100, 'max' => 500, 'step' => 1];
    $accelerationBounds = $rangeBounds['acceleration'] ?? ['min' => 4.0, 'max' => 15.0, 'step' => 0.1];
    $engineTypeOptions = $filterOptions['engine_types'] ?? [];
    $transmissionOptions = $filterOptions['transmissions'] ?? [];
    $driveTypeOptions = $filterOptions['drive_types'] ?? [];
    $selectedEngineTypes = is_array($filters['engine_types'] ?? null) ? $filters['engine_types'] : [];
    $selectedTransmissions = is_array($filters['transmissions'] ?? null) ? $filters['transmissions'] : [];
    $selectedDriveTypes = is_array($filters['drive_types'] ?? null) ? $filters['drive_types'] : [];
    $activeFilterCount = count($selectedEngineTypes) + count($selectedTransmissions) + count($selectedDriveTypes);

    foreach ([
        'price_min',
        'price_max',
        'engine_capacity_min',
        'engine_capacity_max',
        'fuel_combined_min',
        'fuel_combined_max',
        'horsepower_min',
        'horsepower_max',
        'acceleration_min',
        'acceleration_max',
    ] as $filterKey) {
        if (($filters[$filterKey] ?? null) !== null) {
            $activeFilterCount++;
        }
    }

    if ($selectedBrand !== null) {
        $activeFilterCount++;
    }
@endphp

<div @class(['search-filter', $class])>
    <form class="search-filter__form" id="{{ $formId }}" method="GET" action="{{ $action }}" data-search-form novalidate>
        <div class="search-filter__lead">
            <div class="search-filter__intro">
                <h2>{{ $title }}</h2>
                @if (filled($subtitle))
                    <p>{{ $subtitle }}</p>
                @endif
            </div>
            <div class="search-filter__lead-meta">
                @if ($activeFilterCount > 0)
                    <span class="search-filter__count">{{ $activeFilterCount }}</span>
                @endif
            </div>
        </div>

        <div class="search-filter__grid">
            <label class="search-filter__field">
                <span>Модель</span>
                <input type="search" name="q" value="{{ $query }}" placeholder="Например, Geely Monjaro">
            </label>

            <div class="search-filter__field">
                <span>Бренд</span>
                <x-site.brand-select
                    name="brand"
                    :brands="$brandOptions"
                    :selected="$selectedBrand"
                />
            </div>

            <div class="search-filter__field">
                <span>Цена</span>
                <x-site.range-picker
                    name="price"
                    :min="$priceBounds['min']"
                    :max="$priceBounds['max']"
                    :step="$priceBounds['step']"
                    :min-value="$filters['price_min'] ?? null"
                    :max-value="$filters['price_max'] ?? null"
                    :group-thousands="true"
                />
            </div>

            <div class="search-filter__field">
                <span>Объем двигателя, л</span>
                <x-site.range-picker
                    name="engine_capacity"
                    :min="$engineBounds['min']"
                    :max="$engineBounds['max']"
                    :step="$engineBounds['step']"
                    :min-value="$filters['engine_capacity_min'] ?? null"
                    :max-value="$filters['engine_capacity_max'] ?? null"
                    unit="л"
                    :decimals="1"
                />
            </div>

            <div class="search-filter__field" role="group" aria-labelledby="{{ $formId }}-engine-types-label">
                <span id="{{ $formId }}-engine-types-label">Тип топлива</span>
                <div class="search-filter__choices">
                    @foreach ($engineTypeOptions as $option)
                        <label class="search-filter__choice">
                            <input
                                type="checkbox"
                                name="engine_types[]"
                                value="{{ $option['value'] }}"
                                @checked(in_array($option['value'], $selectedEngineTypes, true))
                            >
                            <span>{{ $option['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="search-filter__field" role="group" aria-labelledby="{{ $formId }}-transmissions-label">
                <span id="{{ $formId }}-transmissions-label">Коробка передач</span>
                <div class="search-filter__choices">
                    @foreach ($transmissionOptions as $option)
                        <label class="search-filter__choice">
                            <input
                                type="checkbox"
                                name="transmissions[]"
                                value="{{ $option['value'] }}"
                                @checked(in_array($option['value'], $selectedTransmissions, true))
                            >
                            <span>{{ $option['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="search-filter__field">
                <span>Расход топлива, л/100 км</span>
                <x-site.range-picker
                    name="fuel_combined"
                    :min="$fuelBounds['min']"
                    :max="$fuelBounds['max']"
                    :step="$fuelBounds['step']"
                    :min-value="$filters['fuel_combined_min'] ?? null"
                    :max-value="$filters['fuel_combined_max'] ?? null"
                    unit="л"
                    :decimals="1"
                />
            </div>

            <div class="search-filter__field" role="group" aria-labelledby="{{ $formId }}-drive-types-label">
                <span id="{{ $formId }}-drive-types-label">Привод</span>
                <div class="search-filter__choices">
                    @foreach ($driveTypeOptions as $option)
                        <label class="search-filter__choice">
                            <input
                                type="checkbox"
                                name="drive_types[]"
                                value="{{ $option['value'] }}"
                                @checked(in_array($option['value'], $selectedDriveTypes, true))
                            >
                            <span>{{ $option['label'] }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="search-filter__field">
                <span>Мощность, л.с.</span>
                <x-site.range-picker
                    name="horsepower"
                    :min="$powerBounds['min']"
                    :max="$powerBounds['max']"
                    :step="$powerBounds['step']"
                    :min-value="$filters['horsepower_min'] ?? null"
                    :max-value="$filters['horsepower_max'] ?? null"
                    unit="л.с."
                />
            </div>

            <div class="search-filter__field">
                <span>Разгон 0-100 км/ч, сек.</span>
                <x-site.range-picker
                    name="acceleration"
                    :min="$accelerationBounds['min']"
                    :max="$accelerationBounds['max']"
                    :step="$accelerationBounds['step']"
                    :min-value="$filters['acceleration_min'] ?? null"
                    :max-value="$filters['acceleration_max'] ?? null"
                    unit="с"
                    :decimals="1"
                />
            </div>
        </div>

        <div class="search-filter__actions" data-search-actions>
            <button class="search-filter__submit" type="submit" data-search-submit>Подобрать авто</button>
            <a class="search-filter__reset" href="{{ $resetUrl }}" data-search-reset>Сбросить фильтры</a>
        </div>
    </form>
</div>
