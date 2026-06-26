@props([
    'name',
    'min' => 0,
    'max' => 100,
    'step' => 1,
    'minValue' => null,
    'maxValue' => null,
    'unit' => '',
    'decimals' => 0,
    'groupThousands' => false,
])

@php
    $fmt = static function ($v) use ($decimals, $groupThousands) {
        if ($v === null || $v === '') {
            return '';
        }

        if ($groupThousands) {
            return number_format((float) $v, 0, ',', ' ');
        }

        return rtrim(rtrim(number_format((float) $v, (int) $decimals, '.', ''), '0'), '.');
    };
    $inputMinValue = $minValue !== null && $minValue !== '' ? $fmt($minValue) : '';
    $inputMaxValue = $maxValue !== null && $maxValue !== '' ? $fmt($maxValue) : '';
    $inputType = $groupThousands ? 'text' : 'number';
    $inputMode = $groupThousands ? 'numeric' : 'decimal';
@endphp

<div class="rangepicker">
    <div class="rangepicker__inputs">
        <label class="rangepicker__input">
            <span>От</span>
            <input
                type="{{ $inputType }}"
                name="{{ $name }}_min"
                @unless ($groupThousands)
                    min="{{ $min }}"
                    max="{{ $max }}"
                    step="{{ $step }}"
                @endunless
                value="{{ $inputMinValue }}"
                inputmode="{{ $inputMode }}"
                @if ($groupThousands)
                    data-grouped-number
                    autocomplete="off"
                @endif
            >
            @if ($unit !== '')
                <em>{{ $unit }}</em>
            @endif
        </label>
        <span class="rangepicker__sep" aria-hidden="true">—</span>
        <label class="rangepicker__input">
            <span>До</span>
            <input
                type="{{ $inputType }}"
                name="{{ $name }}_max"
                @unless ($groupThousands)
                    min="{{ $min }}"
                    max="{{ $max }}"
                    step="{{ $step }}"
                @endunless
                value="{{ $inputMaxValue }}"
                inputmode="{{ $inputMode }}"
                @if ($groupThousands)
                    data-grouped-number
                    autocomplete="off"
                @endif
            >
            @if ($unit !== '')
                <em>{{ $unit }}</em>
            @endif
        </label>
    </div>
</div>
