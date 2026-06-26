@extends('layouts.site')

@section('title', 'Новые автомобили '.$year)

@section('content')
    @php
        $displayYear = (int) $year;
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
        $newCarsCurrentPage = $newCars->currentPage();
        $newCarsLastPage = $newCars->lastPage();
        $newCarsPaginationItems = $buildPaginationItems($newCarsCurrentPage, $newCarsLastPage);

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
        <h1>{{ $pageH1 ?? ('Новые автомобили ' . $displayYear) }}</h1>
        <p>Последние новинки автопрома: фото, комплектации и цены на новые авто российского и зарубежного производства
            {{ $displayYear }} года.</p>

        <ul class="ter">
            @foreach ($navigationYears as $navigationYear)
                <li>
                    <a
                        @class(['is-active' => $isTerActive('/new-cars-'.$navigationYear.'/')])
                        href="/new-cars-{{ $navigationYear }}/"
                    >
                        Новые автомобили {{ $navigationYear }}
                    </a>
                </li>
            @endforeach
            <li>
                <a @class(['is-active' => $isTerActive('/electric-cars/')]) href="/electric-cars/">Электромобили</a>
            </li>
        </ul>

        <h2>Новые автомобили: сейчас в продаже</h2>

        @if ($newCarsLastPage > 1)
            <nav class="test_page_div_2" aria-label="Страницы списка новых автомобилей">
                <ul class="test_page_2">
                    @if ($newCarsCurrentPage > 1)
                        <li>
                            <a href="{{ $newCars->url($newCarsCurrentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                        </li>
                    @endif
                    @foreach ($newCarsPaginationItems as $paginationItem)
                        <li>
                            @if ($paginationItem === 'ellipsis')
                                <span class="is-ellipsis" aria-hidden="true">…</span>
                            @elseif ($paginationItem === $newCarsCurrentPage)
                                <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                            @else
                                <a href="{{ $newCars->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                            @endif
                        </li>
                    @endforeach
                    @if ($newCarsCurrentPage < $newCarsLastPage)
                        <li>
                            <a href="{{ $newCars->url($newCarsCurrentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif

        @if ($newCars->isEmpty())
            <p style="padding-left:20px;">Новых автомобилей за {{ $displayYear }} год не найдено.</p>
        @else
            <ul style="float:none;" class="modeli">
                @foreach ($newCars as $car)
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

        @if ($newCarsLastPage > 1)
            <nav class="test_page_div_2" aria-label="Страницы списка новых автомобилей">
                <ul class="test_page_2">
                    @if ($newCarsCurrentPage > 1)
                        <li>
                            <a href="{{ $newCars->url($newCarsCurrentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                        </li>
                    @endif
                    @foreach ($newCarsPaginationItems as $paginationItem)
                        <li>
                            @if ($paginationItem === 'ellipsis')
                                <span class="is-ellipsis" aria-hidden="true">…</span>
                            @elseif ($paginationItem === $newCarsCurrentPage)
                                <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                            @else
                                <a href="{{ $newCars->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                            @endif
                        </li>
                    @endforeach
                    @if ($newCarsCurrentPage < $newCarsLastPage)
                        <li>
                            <a href="{{ $newCars->url($newCarsCurrentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif

        <ul style="margin-bottom:50px;" class="ter">
            @foreach ($navigationYears as $navigationYear)
                <li>
                    <a
                        @class(['is-active' => $isTerActive('/new-cars-'.$navigationYear.'/')])
                        href="/new-cars-{{ $navigationYear }}/"
                    >
                        Новые автомобили {{ $navigationYear }}
                    </a>
                </li>
            @endforeach
            <li>
                <a @class(['is-active' => $isTerActive('/electric-cars/')]) href="/electric-cars/">Электромобили</a>
            </li>
        </ul>
    </section>
@endsection
