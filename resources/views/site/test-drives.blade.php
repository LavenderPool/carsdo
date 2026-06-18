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
            <ul class="test_page">
                <li>
                    <a
                        style="{{ $selectedTestDriveBrand || $isElectricOnly ? '' : 'color:#ff0000; font-weight:bold;' }}"
                        href="/test-drive/"
                    >
                        Последние
                    </a>
                </li>
                <li>
                    <a
                        style="{{ $isElectricOnly ? 'color:#ff0000; font-weight:bold;' : '' }}"
                        href="/test-drive/electric-cars/"
                    >
                        Электромобили
                    </a>
                </li>
                @foreach($testDriveBrands as $testDriveBrand)
                    <li>
                        <a
                            style="{{ $selectedTestDriveBrand?->id === $testDriveBrand->id ? 'color:#ff0000; font-weight:bold;' : '' }}"
                            href="/test-drive/{{ $testDriveBrand->slug }}/"
                        >
                            {{ $testDriveBrand->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
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
