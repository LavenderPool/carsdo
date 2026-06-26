@extends('layouts.site')

@section('title', 'Популярные автомобили')

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
    @endphp

    <section class="block_modeli popular-cars-page">
        <h1>{{ $pageH1 ?? 'Популярные автомобили' }}</h1>
        <p>40 моделей, которыми чаще всего интересуются посетители сайта.</p>

        @if ($popularCars->isEmpty())
            <p style="padding-left:20px;">Популярные автомобили пока не найдены.</p>
        @else
            <ul class="modeli">
                @foreach ($popularCars as $car)
                    <x-site.car-card
                        :href="'/'.$car->brand->slug.'/'.$car->slug.'/'"
                        :name="$car->name"
                        :image="$car->coverUrl()"
                        :price-text="$formatPriceRange($car)"
                        :price-currency="$car->resolvedPriceCurrency()"
                        :is-new="$car->is_soon"
                        :year="$car->year"
                        :is-electric="$car->is_electric_car"
                    />
                @endforeach
            </ul>
        @endif
    </section>
@endsection
