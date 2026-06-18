@php
    $carPath = isset($carPath) && is_string($carPath) && $carPath !== ''
        ? $carPath
        : '/'.$brand->slug.'/'.$car->slug;
    $currentCitySlug = isset($currentCitySlug) && is_string($currentCitySlug) ? $currentCitySlug : null;
    $dealerCitiesToggleId = isset($dealerCitiesToggleId) && is_string($dealerCitiesToggleId) && $dealerCitiesToggleId !== ''
        ? $dealerCitiesToggleId
        : 'dealer-cities-extra';

    $dealerCities = $car->carDealers
        ->filter(fn ($carDealer) => filled($carDealer->city?->slug) && filled($carDealer->city?->name))
        ->map(fn ($carDealer) => (object) [
            'slug' => $carDealer->city->slug,
            'name' => $carDealer->city->name,
        ])
        ->unique('slug')
        ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
        ->values();

    $primaryDealerCities = $dealerCities->take(22);
    $secondaryDealerCities = $dealerCities->slice(22)->values();
@endphp

@if ($dealerCities->isNotEmpty())
    <div id="block_city">
        <div class="title_city"><h3>Дилеры</h3></div>
        <p style="color:#fff;">Где купить {{ $brand->name }} {{ $car->name }} в России.</p>

        <div class="kupmos">
            @foreach ($primaryDealerCities as $dealerCity)
                @if ($dealerCity->slug === $currentCitySlug)
                    <span>{{ $dealerCity->name }}</span>
                @else
                    <a href="{{ $carPath.'/'.$dealerCity->slug }}">{{ $dealerCity->name }}</a>
                @endif
            @endforeach

            @if ($secondaryDealerCities->isNotEmpty())
                <div class="dopcity">
                    <a id="{{ $dealerCitiesToggleId }}-link" href="#{{ $dealerCitiesToggleId }}" onclick="toggleDealerCities('{{ $dealerCitiesToggleId }}'); return false">Другие города</a>
                </div>

                <div id="{{ $dealerCitiesToggleId }}" style="display: none;">
                    @foreach ($secondaryDealerCities as $dealerCity)
                        @if ($dealerCity->slug === $currentCitySlug)
                            <span>{{ $dealerCity->name }}</span>
                        @else
                            <a href="{{ $carPath.'/'.$dealerCity->slug }}">{{ $dealerCity->name }}</a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @once
        <script>
            function toggleDealerCities(elementId) {
                var element = document.getElementById(elementId);

                if (!element) {
                    return;
                }

                element.style.display = element.style.display === 'block' ? 'none' : 'block';
            }
        </script>
    @endonce
@endif
