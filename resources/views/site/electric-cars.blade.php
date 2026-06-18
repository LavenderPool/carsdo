@extends('layouts.site')

@section('title', 'Электромобили в России')

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

        $activePageStyle = 'background:linear-gradient(135deg,#e93737,#b40e0e);';
        $showSoonElectricCars = $electricCars->currentPage() === 1 && $soonElectricCars->isNotEmpty();
    @endphp

    <section class="block_modeli">
        <h1 style="margin-top:20px; padding-left:20px;">{{ $pageH1 ?? 'Электромобили в России' }}</h1>
        <p>Каталог новых электромобилей в России {{ now()->year }}. Цены и комплектации на новые электромобили. Тест-драйвы и
            фото будущих новинок.</p>

        <ul class="ter">
            <li><a href="/new-cars-{{ $catalogPrevYear }}/">Новые автомобили {{ $catalogPrevYear }}</a></li>
            <li><a href="/new-cars-{{ $catalogPrevTwoYear }}/">Новые автомобили {{ $catalogPrevTwoYear }}</a></li>
            <li><a href="/new-cars-{{ $catalogPrevTwoYear - 1 }}/">Новые автомобили {{ $catalogPrevTwoYear - 1 }}</a></li>
        </ul>

        <h2 style="margin:55px 15px 7px 20px;">Новые электромобили: сейчас в продаже</h2>

        @if ($electricCars->lastPage() > 1)
            <div class="test_page_div_2">
                <ul class="test_page_2">
                    @for ($page = 1; $page <= $electricCars->lastPage(); $page++)
                        <li>
                            @if ($page === $electricCars->currentPage())
                                <a style="{{ $activePageStyle }}" href="#">{{ $page }}</a>
                            @else
                                <a href="{{ $electricCars->url($page) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endfor
                </ul>
            </div>
        @endif

        @if ($electricCars->isEmpty())
            <p style="padding-left:20px;">Сейчас в продаже электромобилей не найдено.</p>
        @else
            <ul style="float:none;" class="modeli">
                @foreach ($electricCars as $car)
                    <li>
                        <a class="model_auto_a" href="/{{ $car->brand->slug }}/{{ $car->slug }}/">
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

        @if ($showSoonElectricCars)
            <h2 style="margin:75px 15px 7px 20px;">Новые электромобили: скоро в продаже</h2>
            <ul style="float:none;" class="modeli">
                @foreach ($soonElectricCars as $car)
                    <li>
                        <a class="model_auto_a" href="/{{ $car->brand->slug }}/{{ $car->slug }}/">
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

        @if ($electricCars->lastPage() > 1)
            <div class="test_page_div_2">
                <ul class="test_page_2">
                    @for ($page = 1; $page <= $electricCars->lastPage(); $page++)
                        <li>
                            @if ($page === $electricCars->currentPage())
                                <a style="{{ $activePageStyle }}" href="#">{{ $page }}</a>
                            @else
                                <a href="{{ $electricCars->url($page) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endfor
                </ul>
            </div>
        @endif

        <ul style="margin-bottom:50px;" class="ter">
            <li><a href="/new-cars-{{ $catalogPrevYear }}/">Новые автомобили {{ $catalogPrevYear }}</a></li>
            <li><a href="/new-cars-{{ $catalogPrevTwoYear }}/">Новые автомобили {{ $catalogPrevTwoYear }}</a></li>
            <li><a href="/new-cars-{{ $catalogPrevTwoYear - 1 }}/">Новые автомобили {{ $catalogPrevTwoYear - 1 }}</a></li>
        </ul>
    </section>
@endsection
