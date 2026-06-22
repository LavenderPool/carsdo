@props([
    'baseUrl',
    'brands' => collect(),
    'selectedBrand' => null,
    'showElectric' => false,
    'electricUrl' => null,
    'isElectricOnly' => false,
    'latestLabel' => 'Последние',
])

@php
    $listId = 'brand-filter-select-' . uniqid();
    $normalizedBaseUrl = rtrim((string) $baseUrl, '/') . '/';
    $normalizedElectricUrl = $electricUrl ? rtrim((string) $electricUrl, '/') . '/' : null;
    $selectedLabel = $latestLabel;

    if ($showElectric && $isElectricOnly) {
        $selectedLabel = 'Электромобили';
    } elseif ($selectedBrand) {
        $selectedLabel = $selectedBrand->name;
    }
@endphp

<div class="brand-filter-select" data-brand-filter-select>
    <button
        type="button"
        class="brand-filter-select__trigger"
        data-brand-filter-trigger
        aria-expanded="false"
        aria-haspopup="listbox"
        aria-controls="{{ $listId }}"
    >
        <span class="brand-filter-select__trigger-label">{{ $selectedLabel }}</span>
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path fill="currentColor" d="M6.47 8.72a.75.75 0 0 1 1.06 0L12 13.19l4.47-4.47a.75.75 0 1 1 1.06 1.06l-5 5a.75.75 0 0 1-1.06 0l-5-5a.75.75 0 0 1 0-1.06Z"/>
        </svg>
    </button>

    <div class="brand-filter-select__panel" id="{{ $listId }}" data-brand-filter-panel hidden>
        <div class="brand-filter-select__search">
            <input
                type="text"
                data-brand-filter-search
                placeholder="Поиск бренда"
                autocomplete="off"
            >
        </div>

        <ul class="brand-filter-select__list" role="listbox" data-brand-filter-list>
            <li class="brand-filter-select__item brand-filter-select__item--fixed">
                <button
                    type="button"
                    class="brand-filter-select__option {{ !$selectedBrand && !$isElectricOnly ? 'brand-filter-select__option--active' : '' }}"
                    data-brand-filter-option
                    data-brand-filter-fixed="latest"
                    data-search-text="{{ $latestLabel }}"
                    data-url="{{ $normalizedBaseUrl }}"
                    role="option"
                    aria-selected="{{ !$selectedBrand && !$isElectricOnly ? 'true' : 'false' }}"
                >
                    {{ $latestLabel }}
                </button>
            </li>

            @if($showElectric && $normalizedElectricUrl)
                <li class="brand-filter-select__item">
                    <button
                        type="button"
                        class="brand-filter-select__option {{ $isElectricOnly ? 'brand-filter-select__option--active' : '' }}"
                        data-brand-filter-option
                        data-search-text="Электромобили"
                        data-url="{{ $normalizedElectricUrl }}"
                        role="option"
                        aria-selected="{{ $isElectricOnly ? 'true' : 'false' }}"
                    >
                        Электромобили
                    </button>
                </li>
            @endif

            @foreach($brands as $brand)
                <li class="brand-filter-select__item">
                    <button
                        type="button"
                        class="brand-filter-select__option {{ $selectedBrand?->id === $brand->id ? 'brand-filter-select__option--active' : '' }}"
                        data-brand-filter-option
                        data-search-text="{{ $brand->name }}"
                        data-url="{{ $normalizedBaseUrl . $brand->slug . '/' }}"
                        role="option"
                        aria-selected="{{ $selectedBrand?->id === $brand->id ? 'true' : 'false' }}"
                    >
                        {{ $brand->name }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="brand-filter-select__empty" data-brand-filter-empty hidden>Бренд не найден</div>
    </div>
</div>
