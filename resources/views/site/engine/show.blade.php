@extends('layouts.site')

@php
    $formatCount = static fn ($value): string => number_format((int) $value, 0, ',', ' ');
    $formatDisplacement = static function ($value): ?string {
        if (! filled($value)) {
            return null;
        }

        $normalized = trim((string) $value);

        return is_numeric($normalized)
            ? rtrim(rtrim(number_format(((float) $normalized) / 1000, 1, '.', ''), '0'), '.') . ' л'
            : $normalized;
    };
    $specs = collect([
        'Тип двигателя' => $engine->engine_type,
        'Объем' => $formatDisplacement($engine->displacement_cc),
        'Мощность' => filled($engine->max_horsepower) ? $engine->max_horsepower . ' л.с.' : null,
        'Макс. мощность, об/мин' => $engine->max_power_output_at_rpm,
        'Макс. крутящий момент' => $engine->max_torque_at_rpm,
        'Клапанов на цилиндр' => $engine->valves_per_cylinder,
        'Степень сжатия' => $engine->compression_ratio,
        'Диаметр цилиндра' => filled($engine->cylinder_bore_mm) ? $engine->cylinder_bore_mm . ' мм' : null,
        'Ход поршня' => filled($engine->piston_stroke_mm) ? $engine->piston_stroke_mm . ' мм' : null,
        'Газораспределение' => $engine->valvetrain,
        'Рекомендуемое топливо' => $engine->recommended_fuel_type,
        'Расход топлива' => filled($engine->fuel_consumption_l_per_100_km) ? $engine->fuel_consumption_l_per_100_km . ' л / 100 км' : null,
        'Выбросы CO2' => filled($engine->co2_emissions_g_per_km) ? $engine->co2_emissions_g_per_km . ' г / км' : null,
        'Start / Stop' => $engine->has_start_stop_system === null ? null : ($engine->has_start_stop_system ? 'Есть' : 'Нет'),
    ])->filter(fn ($value) => filled($value));
    $viewsCountLabel = $viewsCountLabel ?? ($formatCount($engine->views_count) . ' просмотров');
@endphp

@section('title', $brand->name . ' ' . $engine->name)

@section('content')
    <section class="engine-page engine-page--detail">
        <div class="engine-hero">
            <div class="engine-hero__head">
                <div class="engine-hero__brand">
                    <img
                        class="engine-hero__logo"
                        data-brand-logo
                        data-brand-slug="{{ $brand->slug }}"
                        alt="{{ $brand->name }}"
                        width="52"
                        height="52"
                        loading="eager"
                    >
                    <div>
                        <a class="engine-hero__brand-link" href="{{ route('engine.brand', ['brand' => $brand->slug]) }}">{{ $brand->name }}</a>
                        <h1>{{ $pageH1 ?? ($brand->name . ' ' . $engine->name) }}</h1>
                    </div>
                </div>

                <div class="engine-hero__meta">
                    <span>{{ $viewsCountLabel }}</span>
                </div>
            </div>

            <div class="engine-hero__badges">
                @if (filled($engine->engine_type))
                    <span class="engine-hero__badge">{{ $engine->engine_type }}</span>
                @endif
                @if ($formatDisplacement($engine->displacement_cc))
                    <span class="engine-hero__badge">{{ $formatDisplacement($engine->displacement_cc) }}</span>
                @endif
                @if (filled($engine->max_horsepower))
                    <span class="engine-hero__badge">{{ $engine->max_horsepower }} л.с.</span>
                @endif
            </div>
        </div>

        @if ($specs->isNotEmpty())
            <div class="engine-page__section">
                <div class="engine-page__section-head">
                    <h2>Характеристики двигателя</h2>
                    <span>{{ $specs->count() }}</span>
                </div>

                <dl class="engine-specs">
                    @foreach ($specs as $label => $value)
                        <div class="engine-specs__item">
                            <dt>{{ $label }}</dt>
                            <dd>{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endif

        @if (filled($engine->engine_notes))
            <div class="engine-page__section">
                <div class="engine-page__section-head">
                    <h2>Описание</h2>
                </div>

                <div class="engine-copy">
                    {!! nl2br(e($engine->engine_notes)) !!}
                </div>
            </div>
        @endif

        {{-- @if (filled($engine->page_text))
            <div class="engine-page__section">
                <div class="engine-page__section-head">
                    <h2>Подробности</h2>
                </div>

                <div class="new_eq engine-copy">
                    {!! $engine->page_text !!}
                </div>
            </div>
        @endif --}}

    </section>
@endsection
