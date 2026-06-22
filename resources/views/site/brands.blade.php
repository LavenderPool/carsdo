@extends('layouts.site')

@section('title', 'Марки автомобилей')
@section('hideFooterBrands', '1')

@section('content')
    <section class="brands-index">
        <h1>Марки автомобилей</h1>
        <p class="brands-index__intro">Все бренды, представленные на сайте, с количеством доступных автомобилей.</p>
        <div class="brands-index__sort" aria-label="Сортировка брендов">
            <a
                class="brands-index__sort-link {{ $sort === 'count' ? 'is-active' : '' }}"
                href="{{ request()->fullUrlWithQuery(['sort' => 'count']) }}"
            >
                По количеству
            </a>
            <a
                class="brands-index__sort-link {{ $sort === 'alphabet' ? 'is-active' : '' }}"
                href="{{ request()->fullUrlWithQuery(['sort' => 'alphabet']) }}"
            >
                По алфавиту
            </a>
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
@endsection
