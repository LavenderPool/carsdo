@extends('layouts.site')

@section('title', $selectedPhotoBrand ? 'Фото новых автомобилей ' . $selectedPhotoBrand->name : 'Фото новых автомобилей')

@section('content')
    <section class="block_modeli">
        <h1 style="margin:20px 0 7px; text-align:center;">
            {{ $pageH1 ?? ($selectedPhotoBrand ? 'Фото новых автомобилей '.$selectedPhotoBrand->name : 'Фото новых автомобилей') }}
        </h1>

        <div class="test_page_div">
            <ul class="test_page">
                <li>
                    <a
                        style="{{ $selectedPhotoBrand ? '' : 'color:#ff0000; font-weight:bold;' }}"
                        href="/cars-photo/"
                    >
                        Последние
                    </a>
                </li>
                @foreach ($photoBrands as $photoBrand)
                    <li>
                        <a
                            style="{{ $selectedPhotoBrand?->id === $photoBrand->id ? 'color:#ff0000; font-weight:bold;' : '' }}"
                            href="/cars-photo/{{ $photoBrand->slug }}/"
                        >
                            {{ $photoBrand->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        @if ($carsWithPhotos->isEmpty())
            <p style="padding-left:20px;">Автомобили с фото пока не найдены.</p>
        @else
            <ul class="modeli">
                @foreach ($carsWithPhotos as $car)
                    @php
                        $cardBrand = $car->brand;
                    @endphp
                    @continue(!$cardBrand)
                    <li>
                        <a class="model_auto_a" href="/{{ $cardBrand->slug }}/{{ $car->slug }}/photo">
                            <span class="model_auto_photo">
                                <img alt="{{ $car->name }}" src="{{ $car->coverUrl() }}">
                            </span>
                            <h3 class="model_auto_name">{{ $car->name }}</h3>
                            <div class="model_auto_price"></div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
