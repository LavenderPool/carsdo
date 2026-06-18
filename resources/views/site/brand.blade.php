@extends('layouts.site')

@section('title', 'Модельный ряд ' . $brand->name)

@section('content')
    @php
        $formatPriceValue = static fn (?int $price): string => filled($price) ? number_format((int) $price, 0, ',', ' ') : '';
        $formatPriceRange = static function ($car) use ($formatPriceValue): string {
            $startPrice = $car->start_price;
            $endPrice = $car->end_price;

            if (filled($startPrice) && filled($endPrice)) {
                return (int) $startPrice === (int) $endPrice
                    ? $formatPriceValue((int) $startPrice)
                    : $formatPriceValue((int) $startPrice).' - '.$formatPriceValue((int) $endPrice);
            }

            if (filled($startPrice)) {
                return $formatPriceValue((int) $startPrice);
            }

            if (filled($endPrice)) {
                return $formatPriceValue((int) $endPrice);
            }

            return 'не объявлена';
        };
        $hasAnyCars = $currentCars->isNotEmpty() || $soonCars->isNotEmpty() || $otherCars->isNotEmpty();
    @endphp

    <section class="block_modeli">
        <h1 style="margin-top:20px; padding-left:20px;">{{ $pageH1 ?? ($brand->name . ' › Модельный ряд') }}</h1>

        <p>Цены на новый модельный ряд {{ $brand->name }} {{ $currentYear }} в России. Актуальные комплектации и цены, фото
            и тест-драйвы, оборудование и доп опции на новые автомобили {{ $brand->name }} от производителя. <a
                href="/cars-photo/{{ $brand->slug }}/">Фото новых автомобилей {{ $brand->name }}</a>.</p>

        @if (! $hasAnyCars)
            <p>моделей нет</p>
        @else
            @if ($currentCars->isNotEmpty())
                <div class="price_H2">
                    <h2>Последние автомобили {{ $brand->name }}</h2>
                </div>
                <ul class="modeli">
                    @foreach ($currentCars as $car)
                        <li>
                            <a class="model_auto_a" href="/{{ $brand->slug }}/{{ $car->slug }}/">
                                <span class="model_auto_photo">
                                    <img alt="{{ $car->name }}" src="{{ $car->coverUrl() }}">
                                </span>
                                <h3 class="model_auto_name">{{ $car->name }}</h3>
                                <div class="model_auto_price">{{ $formatPriceRange($car) }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($soonCars->isNotEmpty())
                <div></div>
                <div class="price_H2">
                    <h2>Новые модели {{ $brand->name }} <br>Скоро в продаже</h2>
                </div>
                <ul class="modeli">
                    @foreach ($soonCars as $car)
                        <li>
                            <a class="model_auto_a" href="/{{ $brand->slug }}/{{ $car->slug }}/">
                                <span class="model_auto_photo">
                                    <img alt="{{ $car->name }}" src="{{ $car->coverUrl() }}">
                                </span>
                                <h3 class="model_auto_name">{{ $car->name }}</h3>
                                <div class="model_auto_price">{{ $formatPriceRange($car) }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

            @if ($otherCars->isNotEmpty())
                <div></div>
                <div class="price_H2">
                    <h2>Другие модели</h2>
                </div>
                <ul class="modeli">
                    @foreach ($otherCars as $car)
                        <li>
                            <a class="model_auto_a" href="/{{ $brand->slug }}/{{ $car->slug }}/">
                                <span class="model_auto_photo">
                                    <img alt="{{ $brand->name }} {{ $car->name }}" src="{{ $car->coverUrl() }}">
                                </span>
                                <h3 class="model_auto_name">{{ $brand->name }} {{ $car->name }}</h3>
                                <div class="model_auto_price">{{ $formatPriceRange($car) }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </section>
@endsection