@extends('layouts.site')

@section('title', 'Новые автомобили '.$year)

@section('content')
    @php
        $displayYear = (int) $year;
        $activePageStyle = 'background:linear-gradient(135deg,#e93737,#b40e0e);';

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
    @endphp

    <section class="block_modeli">
        <h1 style="margin-top:25px; padding-left:20px;">Новые автомобили {{ $displayYear }}</h1>
        <p>Последние новинки автопрома: фото, комплектации и цены на новые авто российского и зарубежного производства
            {{ $displayYear }} года.</p>

        <ul class="ter">
            @foreach ($navigationYears as $navigationYear)
                <li><a href="/new-cars-{{ $navigationYear }}/">Новые автомобили {{ $navigationYear }}</a></li>
            @endforeach
            <li><a href="/electric-cars/">Электромобили</a></li>
        </ul>

        <h2 style="margin:55px 15px 7px 20px;">Новые автомобили: сейчас в продаже</h2>

        @if ($newCars->lastPage() > 1)
            <div class="test_page_div_2">
                <ul class="test_page_2">
                    @for ($page = 1; $page <= $newCars->lastPage(); $page++)
                        <li>
                            @if ($page === $newCars->currentPage())
                                <a style="{{ $activePageStyle }}" href="#">{{ $page }}</a>
                            @else
                                <a href="{{ $newCars->url($page) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endfor
                </ul>
            </div>
        @endif

        @if ($newCars->isEmpty())
            <p style="padding-left:20px;">Новых автомобилей за {{ $displayYear }} год не найдено.</p>
        @else
            <ul style="float:none;" class="modeli">
                @foreach ($newCars as $car)
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

        @if ($newCars->lastPage() > 1)
            <div class="test_page_div_2">
                <ul class="test_page_2">
                    @for ($page = 1; $page <= $newCars->lastPage(); $page++)
                        <li>
                            @if ($page === $newCars->currentPage())
                                <a style="{{ $activePageStyle }}" href="#">{{ $page }}</a>
                            @else
                                <a href="{{ $newCars->url($page) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endfor
                </ul>
            </div>
        @endif

        <ul style="margin-bottom:50px;" class="ter">
            @foreach ($navigationYears as $navigationYear)
                <li><a href="/new-cars-{{ $navigationYear }}/">Новые автомобили {{ $navigationYear }}</a></li>
            @endforeach
            <li><a href="/electric-cars/">Электромобили</a></li>
        </ul>
    </section>
@endsection
