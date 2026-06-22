@extends('layouts.site')

@section('title', $selectedPhotoBrand ? 'Фото новых автомобилей ' . $selectedPhotoBrand->name : 'Фото новых автомобилей')

@section('content')
    <section class="block_modeli">
        <h1 style="margin:20px 0 7px; text-align:center;">
            {{ $pageH1 ?? ($selectedPhotoBrand ? 'Фото новых автомобилей '.$selectedPhotoBrand->name : 'Фото новых автомобилей') }}
        </h1>

        <div class="test_page_div">
            <x-site.brand-filter-select
                base-url="/cars-photo/"
                :brands="$photoBrands"
                :selected-brand="$selectedPhotoBrand"
            />
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
                    <x-site.car-card
                        :href="'/'.$cardBrand->slug.'/'.$car->slug.'/photo'"
                        :name="$car->name"
                        :image="$car->coverUrl()"
                        :is-new="$car->is_soon"
                        :year="$car->year"
                        :is-electric="$car->is_electric_car"
                    />
                @endforeach
            </ul>
        @endif
    </section>
@endsection
