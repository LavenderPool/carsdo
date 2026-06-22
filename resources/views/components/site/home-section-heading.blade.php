@props([
    'title',
    'subtitle' => null,
    'level' => 'h2',
])

<div class="home-section-heading">
    @if($level === 'h1')
        <h1 class="home-section-heading__title">{{ $title }}</h1>
    @else
        <h2 class="home-section-heading__title">{{ $title }}</h2>
    @endif

    @if(filled($subtitle))
        <p class="homepage_p home-section-heading__subtitle">{{ $subtitle }}</p>
    @endif
</div>
