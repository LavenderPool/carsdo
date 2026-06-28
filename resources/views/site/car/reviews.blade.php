@extends('layouts.site')

@php
    $carPath = '/'.$brand->slug.'/'.$car->slug;
    $goodReviews = $car->reviews
        ->where('type', 'good')
        ->pluck('value')
        ->filter(fn ($value) => filled($value))
        ->values();
    $badReviews = $car->reviews
        ->where('type', 'bad')
        ->pluck('value')
        ->filter(fn ($value) => filled($value))
        ->values();
    $ownerReviews = $car->ownerReviews
        ->sortBy([
            ['import_index', 'asc'],
            ['id', 'asc'],
        ])
        ->values();
@endphp

@section('title', $car->name . ' — отзывы владельцев')

@section('content')
<div class="block1">
    <div class="hleb"><a href="/{{ $brand->slug }}/">Автомобили {{ $brand->name }}</a></div>

    <h1>
        <a href="{{ $carPath }}/">{{ $car->name }}</a> › Отзывы владельцев
    </h1>

    <div class="p_test_drive">
        Преимущества, недостатки и личные отзывы владельцев {{ $car->name }},
        основанные на реальном опыте эксплуатации автомобиля.
    </div>

    <div class="new_eq reviews-prc">
        <div class="new_eq2">
            <div class="block_eq1">
                <ul class="komplektatsiya">
                    <li class="reto">Преимущества</li>
                    @forelse ($goodReviews as $goodReview)
                        <li class="ok">{{ $goodReview }}</li>
                    @empty
                        <li class="ok">Преимущества пока не добавлены</li>
                    @endforelse
                </ul>
            </div>

            <div class="block_eq2">
                <ul class="komplektatsiya">
                    <li class="reto">Недостатки</li>
                    @forelse ($badReviews as $badReview)
                        <li class="ok">{{ $badReview }}</li>
                    @empty
                        <li class="ok">Недостатки пока не добавлены</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    @if ($ownerReviews->isNotEmpty())
        <div class="owner-reviews">
            <h2 class="owner-reviews__title">Отзывы владельцев</h2>

            <div class="p_test_drive owner-reviews__intro">
                Реальные отзывы владельцев {{ $car->name }} с оценкой автомобиля, фотографией автора и подробным описанием опыта эксплуатации.
            </div>

            <div class="owner-reviews__list">
                @foreach ($ownerReviews as $ownerReview)
                    @php
                        $rating = max(1, min(5, (int) $ownerReview->rating));
                        $photoUrl = $ownerReview->photoUrl(false);
                    @endphp
                    <article class="owner-review-card">
                        <div class="owner-review-card__head">
                            @if (filled($photoUrl))
                                <img
                                    src="{{ $photoUrl }}"
                                    alt="{{ $ownerReview->full_name }}"
                                    class="owner-review-card__photo"
                                    loading="lazy"
                                >
                            @else
                                <div class="owner-review-card__photo owner-review-card__photo--empty">Фото</div>
                            @endif

                            <div class="owner-review-card__meta">
                                <div class="owner-review-card__name">{{ $ownerReview->full_name }}</div>
                                <div class="owner-review-card__stars" aria-label="Рейтинг {{ $rating }} из 5">
                                    @foreach (range(1, 5) as $star)
                                        <span class="{{ $star <= $rating ? 'is-active' : '' }}">★</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <p class="owner-review-card__text">{{ $ownerReview->text }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>

@include('site.car.partials.galery', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
    'galleryBlockId' => 'reviews-gallery',
])

@include('site.car.partials.specs-and-models', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
])
@endsection