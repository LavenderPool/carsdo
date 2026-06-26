@php
    $formatNumber = static fn (mixed $value): string => $value === null || $value === '' ? '0' : number_format((int) $value, 0, ',', ' ');
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
    $queryTooShort = (bool) ($queryTooShort ?? false);
    $hasSearchableQuery = (bool) ($hasSearchableQuery ?? false);
    $hasActiveFilters = (bool) ($hasActiveFilters ?? false);
    $sort = is_string($sort ?? null) ? $sort : 'popular';
    $sortOptions = is_array($sortOptions ?? null) ? $sortOptions : [];
    $hasSearchInput = $hasQuery || $hasActiveFilters;
    $showBrands = $hasSearchableQuery && $brandsCount > 0;
    $hasResults = $showBrands || $modelsCount > 0;
    $showPopularDefault = ! $hasSearchInput && ! $hasSearchableQuery;
    $minSearchQueryLength = (int) ($minSearchQueryLength ?? 2);
@endphp

<section class="search-results" data-search-results>
    @if ($queryTooShort)
        <div class="search-results__empty search-results__empty--soft">
            Поисковый запрос короче {{ $minSearchQueryLength }} символов и не был учтен.
        </div>
    @endif

    @if ($showBrands)
        <section class="search-results__section">
            <div class="search-results__section-head">
                <h2>Бренды</h2>
                <span>{{ $formatNumber($brandsCount) }}</span>
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
                            <span class="brands-index__count">{{ $formatNumber($brand->cars_count) }} авто</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($showPopularDefault || $hasResults || $hasActiveFilters || $hasSearchableQuery)
        <section class="search-results__section">
            <div class="search-results__section-head search-results__section-head--actions">
                <div class="search-results__section-title">
                    <h2>{{ $showPopularDefault ? 'Популярные авто' : 'Модели' }}</h2>
                    <span>{{ $formatNumber($modelsCount) }}</span>
                </div>
                <label class="search-results__sort">
                    <span>Сортировка</span>
                    <select name="sort" form="searchFiltersForm" data-search-sort>
                        @foreach ($sortOptions as $value => $label)
                            <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
            </div>

            @if ($models->isEmpty())
                <div class="search-results__empty search-results__empty--soft">
                    Подходящих моделей по этому запросу пока нет.
                </div>
            @else
                <ul class="modeli modeli-max-3">
                    @foreach ($models as $car)
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
