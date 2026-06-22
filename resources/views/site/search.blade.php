@extends('layouts.site')

@section('title', $query !== '' ? 'Поиск: '.$query : 'Поиск по сайту')

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
        $modelsCurrentPage = $models->currentPage();
        $modelsLastPage = $models->lastPage();
        $modelsPaginationItems = $buildPaginationItems($modelsCurrentPage, $modelsLastPage);
        $brandsCount = $brands->count();
        $modelsCount = $models->total();
        $hasQuery = $query !== '';
        $hasResults = $brandsCount > 0 || $modelsCount > 0;
    @endphp

    <section class="search-results">
        <div class="search-results__hero">
            <h1>Поиск по сайту</h1>
            @if ($hasQuery)
                <p>
                    Результаты по запросу <strong>{{ $query }}</strong>.
                    Найдено брендов: {{ $brandsCount }}, моделей: {{ $modelsCount }}.
                </p>
            @else
                <p>Введите название бренда или модели в строке поиска выше. Результаты появятся на этой странице.</p>
            @endif
        </div>

        @if (! $hasQuery)
            <div class="search-results__empty">
                Введите минимум {{ $minSearchQueryLength }} символа, чтобы найти бренды и модели автомобилей.
            </div>
        @elseif (! $hasResults)
            <div class="search-results__empty">
                По запросу <strong>{{ $query }}</strong> ничего не найдено. Попробуйте изменить написание или использовать
                более короткий запрос.
            </div>
        @else
            @if ($brands->isNotEmpty())
                <section class="search-results__section">
                    <div class="search-results__section-head">
                        <h2>Бренды</h2>
                        <span>{{ $brandsCount }}</span>
                    </div>

                    <ul class="brands-index__grid">
                        @foreach ($brands as $brand)
                            <li>
                                <a class="brands-index__card" href="/{{ $brand->slug }}/">
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
                                    <span class="brands-index__count">{{ $brand->cars_count }} авто</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <section class="search-results__section">
                <div class="search-results__section-head">
                    <h2>Модели</h2>
                    <span>{{ $modelsCount }}</span>
                </div>

                @if ($models->isEmpty())
                    <div class="search-results__empty search-results__empty--soft">
                        Подходящих моделей по этому запросу пока нет.
                    </div>
                @else
                    <ul class="modeli">
                        @foreach ($models as $car)
                            <x-site.car-card
                                :href="'/'.$car->brand->slug.'/'.$car->slug.'/'"
                                :name="$car->name"
                                :image="$car->coverUrl()"
                                :price-text="$formatPriceRange($car)"
                                :is-new="$car->is_soon"
                                :year="$car->year"
                                :is-electric="$car->is_electric_car"
                            />
                        @endforeach
                    </ul>

                    @if ($modelsLastPage > 1)
                        <nav class="test_page_div_2" aria-label="Страницы результатов поиска">
                            <ul class="test_page_2">
                                @if ($modelsCurrentPage > 1)
                                    <li>
                                        <a href="{{ $models->url($modelsCurrentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                                    </li>
                                @endif
                                @foreach ($modelsPaginationItems as $paginationItem)
                                    <li>
                                        @if ($paginationItem === 'ellipsis')
                                            <span class="is-ellipsis" aria-hidden="true">…</span>
                                        @elseif ($paginationItem === $modelsCurrentPage)
                                            <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                                        @else
                                            <a href="{{ $models->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                                        @endif
                                    </li>
                                @endforeach
                                @if ($modelsCurrentPage < $modelsLastPage)
                                    <li>
                                        <a href="{{ $models->url($modelsCurrentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                                    </li>
                                @endif
                            </ul>
                        </nav>
                    @endif
                @endif
            </section>
        @endif
    </section>
@endsection
