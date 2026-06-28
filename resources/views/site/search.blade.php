@extends('layouts.site')

@section('title', 'Подбор авто по параметрам')

@section('content')
    <div class="search-page">
        <div class="search-page__head">
            <h1>Подбор авто по параметрам</h1>
        </div>

        <div class="search-page__layout">
            <section class="search-page__filters">
                <x-site.search-filter
                    :query="$query"
                    :filters="$filters"
                    :filter-options="$filterOptions"
                    :range-bounds="$rangeBounds"
                    :brand-options="$brandOptions"
                    form-id="searchFiltersForm"
                    :action="route('search')"
                    :reset-url="route('search')"
                />
            </section>

            <div class="search-page__results" data-search-results-container>
                @include('site.partials.search-results', [
                    'query' => $query,
                    'queryTooShort' => $queryTooShort,
                    'hasSearchableQuery' => $hasSearchableQuery,
                    'hasActiveFilters' => $hasActiveFilters,
                    'brands' => $brands,
                    'models' => $models,
                    'sort' => is_string($sort ?? null) ? $sort : 'popular',
                    'sortOptions' => $sortOptions,
                    'minSearchQueryLength' => $minSearchQueryLength,
                ])
            </div>
        </div>
    </div>
@endsection
