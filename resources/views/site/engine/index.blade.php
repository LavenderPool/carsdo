@extends('layouts.site')

@section('title', 'Двигатели автомобилей')
@section('hideFooterBrands', '1')

@section('content')
    <section class="engine-page engine-page--index">
        <div class="engine-page__hero">
            <h1>{{ $pageH1 ?? 'Двигатели автомобилей' }}</h1>
            <p class="engine-page__intro">
                Подборка брендов и популярных двигателей с основными характеристиками и переходом на отдельные страницы.
            </p>
        </div>

        <div class="engine-page__section">
            <div class="engine-page__section-head">
                <h2>Бренды</h2>
                <span>{{ $engineBrands->count() }}</span>
            </div>

            @if ($engineBrands->isEmpty())
                <p>Бренды с двигателями пока не добавлены.</p>
            @else
                <ul class="brands-index__grid">
                    @foreach ($engineBrands as $brand)
                        <li>
                            <a class="brands-index__card" href="{{ route('engine.brand', ['brand' => $brand->slug]) }}">
                                <span class="brands-index__head">
                                    <img
                                        class="brands-index__logo"
                                        data-brand-logo
                                        data-brand-slug="{{ $brand->slug }}"
                                        alt="{{ $brand->name }}"
                                        width="44"
                                        height="44"
                                        loading="lazy"
                                    >
                                    <span class="brands-index__name">{{ $brand->name }}</span>
                                </span>
                                <span class="brands-index__count">{{ number_format($brand->engines_count, 0, ',', ' ') }} двигателей</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="engine-page__section">
            <div class="engine-page__section-head">
                <h2>Топ популярных двигателей</h2>
                <span>{{ $popularEngines->count() }}</span>
            </div>

            @if ($popularEngines->isEmpty())
                <p>Популярные двигатели пока не определены.</p>
            @else
                <ul class="engine-cards">
                    @foreach ($popularEngines as $engine)
                        @php
                            $brand = $engine->brand;
                        @endphp
                        @continue(!$brand)
                        <x-site.engine-card
                            :href="route('engine.show', ['brand' => $brand->slug, 'engine_slug' => $engine->slug])"
                            :name="$engine->name"
                            :brand-name="$brand->name"
                            :brand-slug="$brand->slug"
                            :engine-type="$engine->engine_type"
                            :displacement-cc="$engine->displacement_cc"
                            :max-horsepower="$engine->max_horsepower"
                        />
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endsection
