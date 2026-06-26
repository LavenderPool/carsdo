@props([
    'href',
    'name',
    'image',
    'priceText' => null,
    'priceCurrency' => 'руб.',
    'isNew' => false,
    'year' => null,
    'isElectric' => false,
])

@php
    $normalizedPriceText = is_string($priceText) ? trim($priceText) : '';
    $normalizedPriceCurrency = is_string($priceCurrency) ? trim($priceCurrency) : '';
    $hasPrice = $normalizedPriceText !== '' && mb_strtolower($normalizedPriceText) !== 'не объявлена';
    $priceLabel = $hasPrice
        ? $normalizedPriceText.' '.($normalizedPriceCurrency !== '' ? $normalizedPriceCurrency : 'руб.')
        : ($normalizedPriceText !== '' ? $normalizedPriceText : null);

    $badgeText = null;
    if ((bool) $isNew) {
        $badgeText = 'Новая';
    } elseif (filled($year)) {
        $badgeText = (string) $year;
    } elseif ((bool) $isElectric) {
        $badgeText = 'Электро';
    }
@endphp

<li>
    <a class="model_auto_a" href="{{ $href }}">
        <span class="model_auto_photo">
            <img alt="{{ $name }}" src="{{ $image }}" data-car-image="true">
        </span>
        <div class="model_auto_info">
            <div class="model_auto_info_left">
                <h3 class="model_auto_name">{{ $name }}</h3>
                @if ($priceLabel !== null)
                    <div class="model_auto_price">{{ $priceLabel }}</div>
                @endif
            </div>
            <div class="model_auto_info_right">
                @if ($badgeText !== null)
                    <span class="model_auto_badge">{{ $badgeText }}</span>
                @endif
            </div>
        </div>
    </a>
</li>
