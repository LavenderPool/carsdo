@props([
    'href',
    'name',
    'brandName',
    'brandSlug',
    'engineType' => null,
    'displacementCc' => null,
    'maxHorsepower' => null,
    'configurationsCount' => null,
])

@php
    $displacementLabel = null;

    if (filled($displacementCc)) {
        $displacementValue = trim((string) $displacementCc);
        $displacementLabel = is_numeric($displacementValue)
            ? rtrim(rtrim(number_format(((float) $displacementValue) / 1000, 1, '.', ''), '0'), '.') . ' л'
            : $displacementValue;
    }

    $specs = collect([
        filled($engineType) ? (string) $engineType : null,
        $displacementLabel,
        filled($maxHorsepower) ? trim((string) $maxHorsepower) . ' л.с.' : null,
    ])->filter()->values();

    $stats = collect([
        filled($configurationsCount) ? ['label' => 'Конфигураций', 'value' => (int) $configurationsCount] : null,
    ])->filter()->values();
@endphp

<li>
    <a class="engine-card" href="{{ $href }}">
        <span class="engine-card__head">
            <span class="engine-card__brand">
                <img
                    class="engine-card__logo"
                    data-brand-logo
                    data-brand-slug="{{ $brandSlug }}"
                    alt="{{ $brandName }}"
                    width="44"
                    height="44"
                    loading="lazy"
                >
                <span class="engine-card__brand-name">{{ $brandName }}</span>
            </span>
            <span class="engine-card__title">{{ $name }}</span>
        </span>

        @if ($specs->isNotEmpty())
            <span class="engine-card__specs">
                @foreach ($specs as $spec)
                    <span class="engine-card__spec">{{ $spec }}</span>
                @endforeach
            </span>
        @endif

        @if ($stats->isNotEmpty())
            <span class="engine-card__stats">
                @foreach ($stats as $stat)
                    <span class="engine-card__stat">
                        <strong>{{ number_format($stat['value'], 0, ',', ' ') }}</strong>
                        {{ $stat['label'] }}
                    </span>
                @endforeach
            </span>
        @endif
    </a>
</li>
