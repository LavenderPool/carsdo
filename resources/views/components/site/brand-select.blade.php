@props([
    'name' => 'brand',
    'brands' => collect(),
    'selected' => null,
    'placeholder' => 'Любой бренд',
])

@php
    $listId = 'brand-select-' . uniqid();
    $selectedBrand = $brands->firstWhere('slug', $selected);
    $selectedLabel = $selectedBrand?->name ?? $placeholder;
@endphp

<div class="brand-select" data-brand-select>
    <input type="hidden" name="{{ $name }}" value="{{ $selectedBrand?->slug ?? '' }}" data-brand-select-input>

    <button
        type="button"
        class="brand-select__trigger"
        data-brand-select-trigger
        aria-expanded="false"
        aria-haspopup="listbox"
        aria-controls="{{ $listId }}"
    >
        <span class="brand-select__trigger-label {{ $selectedBrand ? '' : 'is-placeholder' }}" data-brand-select-label>{{ $selectedLabel }}</span>
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path fill="currentColor" d="M6.47 8.72a.75.75 0 0 1 1.06 0L12 13.19l4.47-4.47a.75.75 0 1 1 1.06 1.06l-5 5a.75.75 0 0 1-1.06 0l-5-5a.75.75 0 0 1 0-1.06Z"/>
        </svg>
    </button>

    <div class="brand-select__panel" id="{{ $listId }}" data-brand-select-panel hidden>
        <div class="brand-select__search">
            <input type="text" data-brand-select-search placeholder="Поиск бренда" autocomplete="off">
        </div>

        <ul class="brand-select__list" role="listbox" data-brand-select-list>
            <li class="brand-select__item brand-select__item--fixed">
                <button
                    type="button"
                    class="brand-select__option {{ $selectedBrand ? '' : 'brand-select__option--active' }}"
                    data-brand-select-option
                    data-brand-select-fixed="any"
                    data-value=""
                    data-label="{{ $placeholder }}"
                    data-search-text="{{ $placeholder }}"
                    role="option"
                    aria-selected="{{ $selectedBrand ? 'false' : 'true' }}"
                >
                    {{ $placeholder }}
                </button>
            </li>

            @foreach($brands as $brand)
                <li class="brand-select__item">
                    <button
                        type="button"
                        class="brand-select__option {{ $selectedBrand?->id === $brand->id ? 'brand-select__option--active' : '' }}"
                        data-brand-select-option
                        data-value="{{ $brand->slug }}"
                        data-label="{{ $brand->name }}"
                        data-search-text="{{ $brand->name }}"
                        role="option"
                        aria-selected="{{ $selectedBrand?->id === $brand->id ? 'true' : 'false' }}"
                    >
                        {{ $brand->name }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="brand-select__empty" data-brand-select-empty hidden>Бренд не найден</div>
    </div>
</div>
