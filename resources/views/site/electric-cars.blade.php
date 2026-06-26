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

        $showSoonElectricCars = $electricCars->currentPage() === 1 && $soonElectricCars->isNotEmpty();
        $normalizedCurrentUrl = rtrim(url()->current(), '/');
        $isTerActive = static fn (string $path): bool => $normalizedCurrentUrl === rtrim(url($path), '/');
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
        $electricCarsCurrentPage = $electricCars->currentPage();
        $electricCarsLastPage = $electricCars->lastPage();
        $electricCarsPaginationItems = $buildPaginationItems($electricCarsCurrentPage, $electricCarsLastPage);
    @endphp

    <section class="block_modeli">
        <h1>{{ $pageH1 ?? 'Электромобили в России' }}</h1>
        <p>Каталог новых электромобилей в России {{ now()->year }}. Цены и комплектации на новые электромобили. Тест-драйвы и
            фото будущих новинок.</p>

        <ul class="ter">
            <li>
                <a @class(['is-active' => $isTerActive('/new-cars-'.$catalogPrevYear.'/')]) href="/new-cars-{{ $catalogPrevYear }}/">
                    Новые автомобили {{ $catalogPrevYear }}
                </a>
            </li>
            <li>
                <a @class(['is-active' => $isTerActive('/new-cars-'.$catalogPrevTwoYear.'/')]) href="/new-cars-{{ $catalogPrevTwoYear }}/">
                    Новые автомобили {{ $catalogPrevTwoYear }}
                </a>
            </li>
            <li>
                <a
                    @class(['is-active' => $isTerActive('/new-cars-'.($catalogPrevTwoYear - 1).'/')])
                    href="/new-cars-{{ $catalogPrevTwoYear - 1 }}/"
                >
                    Новые автомобили {{ $catalogPrevTwoYear - 1 }}
                </a>
            </li>
        </ul>

        <h2>Новые электромобили: сейчас в продаже</h2>

        @if ($electricCarsLastPage > 1)
            <nav class="test_page_div_2" aria-label="Страницы списка электромобилей">
                <ul class="test_page_2">
                    @if ($electricCarsCurrentPage > 1)
                        <li>
                            <a href="{{ $electricCars->url($electricCarsCurrentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                        </li>
                    @endif
                    @foreach ($electricCarsPaginationItems as $paginationItem)
                        <li>
                            @if ($paginationItem === 'ellipsis')
                                <span class="is-ellipsis" aria-hidden="true">…</span>
                            @elseif ($paginationItem === $electricCarsCurrentPage)
                                <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                            @else
                                <a href="{{ $electricCars->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                            @endif
                        </li>
                    @endforeach
                    @if ($electricCarsCurrentPage < $electricCarsLastPage)
                        <li>
                            <a href="{{ $electricCars->url($electricCarsCurrentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif

        @if ($electricCars->isEmpty())
            <p style="padding-left:20px;">Сейчас в продаже электромобилей не найдено.</p>
        @else
            <ul style="float:none;" class="modeli">
                @foreach ($electricCars as $car)
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

        @if ($showSoonElectricCars)
            <h2 style="margin:75px 15px 7px 20px;">Новые электромобили: скоро в продаже</h2>
            <ul style="float:none;" class="modeli">
                @foreach ($soonElectricCars as $car)
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

        @if ($electricCarsLastPage > 1)
            <nav class="test_page_div_2" aria-label="Страницы списка электромобилей">
                <ul class="test_page_2">
                    @if ($electricCarsCurrentPage > 1)
                        <li>
                            <a href="{{ $electricCars->url($electricCarsCurrentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                        </li>
                    @endif
                    @foreach ($electricCarsPaginationItems as $paginationItem)
                        <li>
                            @if ($paginationItem === 'ellipsis')
                                <span class="is-ellipsis" aria-hidden="true">…</span>
                            @elseif ($paginationItem === $electricCarsCurrentPage)
                                <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                            @else
                                <a href="{{ $electricCars->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                            @endif
                        </li>
                    @endforeach
                    @if ($electricCarsCurrentPage < $electricCarsLastPage)
                        <li>
                            <a href="{{ $electricCars->url($electricCarsCurrentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif

        <ul style="margin-bottom:50px;" class="ter">
            <li>
                <a @class(['is-active' => $isTerActive('/new-cars-'.$catalogPrevYear.'/')]) href="/new-cars-{{ $catalogPrevYear }}/">
                    Новые автомобили {{ $catalogPrevYear }}
                </a>
            </li>
            <li>
                <a @class(['is-active' => $isTerActive('/new-cars-'.$catalogPrevTwoYear.'/')]) href="/new-cars-{{ $catalogPrevTwoYear }}/">
                    Новые автомобили {{ $catalogPrevTwoYear }}
                </a>
            </li>
            <li>
                <a
                    @class(['is-active' => $isTerActive('/new-cars-'.($catalogPrevTwoYear - 1).'/')])
                    href="/new-cars-{{ $catalogPrevTwoYear - 1 }}/"
                >
                    Новые автомобили {{ $catalogPrevTwoYear - 1 }}
                </a>
            </li>
        </ul>
    </section>
@endsection
