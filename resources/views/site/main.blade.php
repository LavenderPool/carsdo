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
        <li>
            <a href="/{{ $crashTest->car->brand->slug }}/{{ $crashTest->car->slug }}/crash-test/">
                <img alt="Краш-тест {{ $crashTest->car->brand->name }} {{ $crashTest->car->name }}" src="{{ $crashTest->car->coverUrl() }}">
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
        <li>
            <a href="/{{ $testDrive->car->brand->slug }}/{{ $testDrive->car->slug }}/test-drive/">
                <img alt="Тест-драйв {{ $testDrive->car->brand->name }} {{ $testDrive->car->name }}" src="{{ $testDrive->car->coverUrl() }}">
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
