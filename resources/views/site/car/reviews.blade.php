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
@endphp

@section('title', $car->name . ' — отзывы владельцев')

@section('content')
<div class="block1">
    <div class="hleb"><a href="/{{ $brand->slug }}/">Автомобили {{ $brand->name }}</a></div>

    <h1 style="padding-left:20px;"><a href="{{ $carPath }}/">{{ $car->name }}</a> › Отзывы владельцев (плюсы и минусы)</h1>

    <div class="p_test_drive">
        Преимущества и недостатки {{ $car->name }}: оценка потребительских качеств автомобиля,
        основанная на опыте эксплуатации реальных владельцев.
    </div>

    <div class="new_eq">
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
</div>

@include('site.car.partials.specs-and-models', [
    'brand' => $brand,
    'car' => $car,
    'carPath' => $carPath,
])
@endsection