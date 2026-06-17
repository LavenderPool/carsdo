@extends('layouts.site')

@section('title', $siteBrandName ?? config('app.name', 'carsDo'))

@section('content')
<div class="block1">


    <div class="homepage_bloсk1">
    <h1 style="padding:0 20px;">Новые автомобили в России</h1>
    <div class="homepage_p">Последние новинки 2025. Официальные комплектации и цены на новые авто в России.</div>
    <div class="newhome">
    <ul class="nch">
    @foreach ($newCars as $car)
        <li>
            <a href="/{{ $car->brand->slug }}/{{ $car->slug }}/">
                <span class="home-top-card">
                    <img alt="{{ $car->brand->name }} {{ $car->name }}" src="{{ $car->coverUrl() }}">
                    <span class="home-top-card__overlay"></span>
                    <span class="home-top-card__title">{{ $car->name }}</span>
                </span>
            </a>
        </li>
    @endforeach
    </ul>
    </div>
    </div>
    
    <div class="homepage_bloсk6">
    <h2 style="padding-left:13px;">Скоро на дорогах</h2>
    <div class="homepage_p">Будущие новинки автопрома 2026 в новом кузове.</div>
    <div class="newhome">
    <ul class="nch">
    @foreach ($soonCars as $car)
        <li>
            <a href="/{{ $car->brand->slug }}/{{ $car->slug }}/">
                <span class="home-top-card">
                    <img alt="{{ $car->brand->name }} {{ $car->name }} Новый кузов" src="{{ $car->coverUrl() }}">
                    <span class="home-top-card__overlay"></span>
                    <span class="home-top-card__title">{{ $car->name }}</span>
                </span>
            </a>
        </li>
    @endforeach
    </ul>
    </div>
    </div>
    
    
    <div class="homepage_bloсk2">
    <h2 style="padding-left:13px;">Краш-тесты</h2>
    <div class="homepage_p">Независимая оценка безопасности вашего будущего автомобиля.</div>
    <div class="homecrash">
    <ul class="ctc">
    @foreach ($crashTests as $crashTest)
        @php
            $car = $crashTest->car;
            $cardBrand = $car?->brand;
            $ratingValue = is_numeric($crashTest->rating ?? null) ? (int) $crashTest->rating : null;
            $ratingStars = $ratingValue && $ratingValue > 0 ? max(1, min(5, $ratingValue)) : null;
        @endphp
        @continue(!$car || !$cardBrand)
        <li>
            <a href="/{{ $cardBrand->slug }}/{{ $car->slug }}/crash-test/">
                <span class="crash-test-card">
                    <img alt="Краш-тест {{ $cardBrand->name }} {{ $car->name }}" src="{{ $car->coverUrl() }}">
                    <span class="crash-test-card__overlay"></span>
                    <span class="crash-test-card__content">
                        <span class="crash-test-card__title">{{ $car->name }}</span>
                        @if($ratingStars)
                            <span class="crash-test-card__stars" aria-label="Рейтинг {{ $ratingStars }} из 5">
                                @foreach(range(1, $ratingStars) as $star)
                                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                                    </svg>
                                @endforeach
                            </span>
                        @endif
                    </span>
                </span>
            </a>
        </li>
    @endforeach
    </ul>
    </div>
    </div>
    
    
    
    
    <div class="homepage_bloсk3">
    <h2 style="padding-left:13px;">Тест-драйвы</h2>
    <div class="homepage_p">Лучшие обзоры новых машин, наша подборка.</div>
    <div class="homecrash">
    <ul class="ctc">
    @foreach ($testDrives as $testDrive)
        @php
            $car = $testDrive->car;
            $cardBrand = $car?->brand;
        @endphp
        @continue(!$car || !$cardBrand)
        <li>
            <a href="/{{ $cardBrand->slug }}/{{ $car->slug }}/test-drive/">
                <span class="test-drive-card">
                    <img alt="Тест-драйв {{ $cardBrand->name }} {{ $car->name }}" src="{{ $car->coverUrl() }}">
                    <span class="test-drive-card__overlay"></span>
                    <span class="test-drive-card__title">{{ $car->name }}</span>
                </span>
            </a>
        </li>
    @endforeach
    </ul>
    </div>
    </div>
    
    
    <div class="homepage_bloсk5">
    <h2 style="padding-left:13px;">Популярные модели</h2>
    <div class="homepage_p">Автомобили, которыми интересуются больше всего на сайте.</div>
    <ul class="spc">
    @foreach ($popularCars as $car)
        <li>
            <a href="/{{ $car->brand->slug }}/{{ $car->slug }}/">{{ $car->name }}</a>
        </li>
    @endforeach
    </ul>
    </div>
    
    
    </div>
@endsection
