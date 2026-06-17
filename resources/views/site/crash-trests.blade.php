@extends('layouts.site')

@section('title', $selectedCrashTestBrand ? 'Краш-тесты ' . $selectedCrashTestBrand->name : 'Краш-тесты')

@section('content')
@php
    $isElectricOnly = $isElectricOnly ?? false;
@endphp
<div class="block_modeli">
    <div style="margin:20px 0 0 0;">
        <h1 style="padding-left:20px;">
            {{ $selectedCrashTestBrand ? 'Краш-тесты ' . $selectedCrashTestBrand->name : 'Краш-тесты' }}
        </h1>
        <div class="homepage_p">Независимая оценка безопасности вашего будущего автомобиля.</div>

        <div class="test_page_div">
            <ul class="test_page">
                <li>
                    <a
                        style="{{ $selectedCrashTestBrand || $isElectricOnly ? '' : 'color:#ff0000; font-weight:bold;' }}"
                        href="/crash-test/"
                    >
                        Последние
                    </a>
                </li>
                <li>
                    <a
                        style="{{ $isElectricOnly ? 'color:#ff0000; font-weight:bold;' : '' }}"
                        href="/crash-test/electric-cars/"
                    >
                        Электромобили
                    </a>
                </li>
                @foreach($crashTestBrands as $crashTestBrand)
                    <li>
                        <a
                            style="{{ $selectedCrashTestBrand?->id === $crashTestBrand->id ? 'color:#ff0000; font-weight:bold;' : '' }}"
                            href="/crash-test/{{ $crashTestBrand->slug }}/"
                        >
                            {{ $crashTestBrand->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="homecrash">
            @if($crashTests->isNotEmpty())
                <ul class="ctc">
                    @foreach($crashTests as $crashTest)
                        @php
                            $car = $crashTest->car;
                            $cardBrand = $car?->brand;
                            $ratingValue = is_numeric($crashTest->rating) ? (int) $crashTest->rating : null;
                            $ratingStars = $ratingValue && $ratingValue > 0 ? max(1, min(5, $ratingValue)) : null;
                        @endphp
                        @continue(!$car || !$cardBrand)
                        <li>
                            <a href="/{{ $cardBrand->slug }}/{{ $car->slug }}/crash-test/">
                                <span class="crash-test-card">
                                    <img
                                        alt="Краш-тест {{ $car->name }}"
                                        src="{{ $car->coverUrl() }}"
                                    >
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
            @else
                <div class="homepage_p">Краш-тесты пока не добавлены.</div>
            @endif
        </div>
    </div>
</div>
@endsection