@extends('layouts.site')

@section('title', $selectedTestDriveBrand ? 'Тест-драйвы ' . $selectedTestDriveBrand->name : 'Тест-драйвы')

@section('content')
@php
    $isElectricOnly = $isElectricOnly ?? false;
@endphp
<div class="block_modeli">
    <div style="margin:20px 0 0 0;">
        <h1 style="padding-left:20px;">
            {{ $pageH1 ?? ($selectedTestDriveBrand ? 'Тест-драйвы ' . $selectedTestDriveBrand->name : 'Тест-драйвы') }}
        </h1>
        <div class="homepage_p">Тест-драйвы новых автомобилей 2026: видео обзор.</div>

        <div class="test_page_div">
            <x-site.brand-filter-select
                base-url="/test-drive/"
                :brands="$testDriveBrands"
                :selected-brand="$selectedTestDriveBrand"
                :show-electric="true"
                electric-url="/test-drive/electric-cars/"
                :is-electric-only="$isElectricOnly"
            />
        </div>

        <div class="homecrash">
            @if($testDrives->isNotEmpty())
                <ul class="ctc">
                    @foreach($testDrives as $testDrive)
                        @php
                            $car = $testDrive->car;
                            $cardBrand = $car?->brand;
                        @endphp
                        @continue(!$car || !$cardBrand)
                        <li>
                            <a href="/{{ $cardBrand->slug }}/{{ $car->slug }}/test-drive/">
                                <span class="test-drive-card">
                                    <img
                                        alt="Тест-драйв {{ $car->name }}"
                                        src="{{ $car->coverUrl() }}"
                                        data-car-image="true"
                                    >
                                    <span class="test-drive-card__overlay"></span>
                                    <span class="test-drive-card__title">{{ $car->name }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="homepage_p">Тест-драйвы пока не добавлены.</div>
            @endif
        </div>
    </div>
</div>
@endsection
