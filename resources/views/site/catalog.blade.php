@extends('layouts.site')

@section('title', $catalog->name)

@section('content')
    @php
        $buildPaginationItems = static function (int $currentPage, int $lastPage): array {
            if ($lastPage < 1) {
                return [];
            }

            $pages = [1, $lastPage];
            $windowStart = max(1, $currentPage - 1);
            $windowEnd = min($lastPage, $currentPage + 1);

            for ($page = $windowStart; $page <= $windowEnd; $page++) {
                $pages[] = $page;
            }

            $pages = array_values(array_unique($pages));
            sort($pages);

            $items = [];
            $previousPage = null;

            foreach ($pages as $page) {
                if ($previousPage !== null && $page - $previousPage > 1) {
                    $items[] = 'ellipsis';
                }

                $items[] = $page;
                $previousPage = $page;
            }

            return $items;
        };

        $carsCurrentPage = $cars->currentPage();
        $carsLastPage = $cars->lastPage();
        $carsPaginationItems = $buildPaginationItems($carsCurrentPage, $carsLastPage);

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
        <h1>{{ $pageH1 ?? $catalog->name }}</h1>
        @if (filled($catalog->description))
            <p>{{ $catalog->description }}</p>
        @endif

        @if ($carsLastPage > 1)
            <nav class="test_page_div_2" aria-label="Страницы каталога автомобилей">
                <ul class="test_page_2">
                    @if ($carsCurrentPage > 1)
                        <li>
                            <a href="{{ $cars->url($carsCurrentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                        </li>
                    @endif
                    @foreach ($carsPaginationItems as $paginationItem)
                        <li>
                            @if ($paginationItem === 'ellipsis')
                                <span class="is-ellipsis" aria-hidden="true">…</span>
                            @elseif ($paginationItem === $carsCurrentPage)
                                <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                            @else
                                <a href="{{ $cars->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                            @endif
                        </li>
                    @endforeach
                    @if ($carsCurrentPage < $carsLastPage)
                        <li>
                            <a href="{{ $cars->url($carsCurrentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif

        @if ($cars->isEmpty())
            <p style="padding-left:20px;">Автомобили в этом каталоге пока не найдены.</p>
        @else
            <ul style="float:none;" class="modeli">
                @foreach ($cars as $car)
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

        @if ($carsLastPage > 1)
            <nav class="test_page_div_2" aria-label="Страницы каталога автомобилей">
                <ul class="test_page_2">
                    @if ($carsCurrentPage > 1)
                        <li>
                            <a href="{{ $cars->url($carsCurrentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                        </li>
                    @endif
                    @foreach ($carsPaginationItems as $paginationItem)
                        <li>
                            @if ($paginationItem === 'ellipsis')
                                <span class="is-ellipsis" aria-hidden="true">…</span>
                            @elseif ($paginationItem === $carsCurrentPage)
                                <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                            @else
                                <a href="{{ $cars->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                            @endif
                        </li>
                    @endforeach
                    @if ($carsCurrentPage < $carsLastPage)
                        <li>
                            <a href="{{ $cars->url($carsCurrentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif
    </section>
@endsection
