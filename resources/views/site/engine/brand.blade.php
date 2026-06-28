@extends('layouts.site')

@section('title', 'Двигатели ' . $selectedEngineBrand->name)

@section('content')
    <section class="engine-page">
        <h1 style="margin:20px 0 7px; text-align:center;">
            {{ $pageH1 ?? ('Двигатели ' . $selectedEngineBrand->name) }}
        </h1>

        <div class="test_page_div">
            <x-site.brand-filter-select
                base-url="/engine/"
                :brands="$engineBrands"
                :selected-brand="$selectedEngineBrand"
                latest-label="Все бренды"
            />
        </div>

        @if ($engines->isEmpty())
            <p style="padding-left:20px;">Для {{ $selectedEngineBrand->name }} двигатели пока не добавлены.</p>
        @else
            <div class="engine-page__summary">
                Найдено {{ number_format($engines->count(), 0, ',', ' ') }} двигателей {{ $selectedEngineBrand->name }}.
            </div>

            <ul class="engine-cards">
                @foreach ($engines as $engine)
                    <x-site.engine-card
                        :href="route('engine.show', ['brand' => $selectedEngineBrand->slug, 'engine_slug' => $engine->slug])"
                        :name="$engine->name"
                        :brand-name="$selectedEngineBrand->name"
                        :brand-slug="$selectedEngineBrand->slug"
                        :engine-type="$engine->engine_type"
                        :displacement-cc="$engine->displacement_cc"
                        :max-horsepower="$engine->max_horsepower"
                        :configurations-count="$engine->configurations_count"
                    />
                @endforeach
            </ul>
        @endif
    </section>
@endsection
