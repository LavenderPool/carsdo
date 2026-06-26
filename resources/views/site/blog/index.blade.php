@extends('layouts.site')

@section('title', 'Блог')

@section('content')
    @php
        $buildPaginationItems = static function (int $currentPage, int $lastPage): array {
            if ($lastPage < 1) {
                return [];
            }

            $pages = [1, $lastPage];
            $windowStart = max(1, $currentPage - 1);
            $windowEnd = min($lastPage, $currentPage + 1);

            for ($page = $windowStart; $page <= $windowEnd; $page++) {
                $pages[] = $page;
            }

            $pages = array_values(array_unique($pages));
            sort($pages);

            $items = [];
            $previousPage = null;

            foreach ($pages as $page) {
                if ($previousPage !== null && $page - $previousPage > 1) {
                    $items[] = 'ellipsis';
                }

                $items[] = $page;
                $previousPage = $page;
            }

            return $items;
        };

        $currentPage = $articles->currentPage();
        $lastPage = $articles->lastPage();
        $paginationItems = $buildPaginationItems($currentPage, $lastPage);
    @endphp

    <section class="block_modeli">
        <h1>{{ $pageH1 ?? 'Блог' }}</h1>
        <p>Новости и статьи об автомобилях: обзоры, аналитика и важные обновления рынка.</p>

        @if ($lastPage > 1)
            <nav class="test_page_div_2" aria-label="Страницы блога">
                <ul class="test_page_2">
                    @if ($currentPage > 1)
                        <li>
                            <a href="{{ $articles->url($currentPage - 1) }}" aria-label="Предыдущая страница">Назад</a>
                        </li>
                    @endif
                    @foreach ($paginationItems as $paginationItem)
                        <li>
                            @if ($paginationItem === 'ellipsis')
                                <span class="is-ellipsis" aria-hidden="true">…</span>
                            @elseif ($paginationItem === $currentPage)
                                <span aria-current="page" aria-label="Страница {{ $paginationItem }}, текущая">{{ $paginationItem }}</span>
                            @else
                                <a href="{{ $articles->url($paginationItem) }}" aria-label="Страница {{ $paginationItem }}">{{ $paginationItem }}</a>
                            @endif
                        </li>
                    @endforeach
                    @if ($currentPage < $lastPage)
                        <li>
                            <a href="{{ $articles->url($currentPage + 1) }}" aria-label="Следующая страница">Вперёд</a>
                        </li>
                    @endif
                </ul>
            </nav>
        @endif

        @if ($articles->isEmpty())
            <p style="padding-left:20px;">Публикации пока не добавлены.</p>
        @else
            <div class="homecrash" style="margin-top:20px;">
                <ul class="ctc">
                    @foreach ($articles as $article)
                        <li>
                            <a href="{{ route('blog.show', $article) }}">
                                <span class="crash-test-card">
                                    <img
                                        src="{{ $article->coverUrl() }}"
                                        alt="{{ $article->title }}"
                                        data-car-image="true"
                                    >
                                    <span class="crash-test-card__overlay"></span>
                                    <span class="crash-test-card__content">
                                        <span class="crash-test-card__title">{{ $article->title }}</span>
                                        <span class="crash-test-card__meta">
                                            {{ optional($article->published_at)->translatedFormat('d F Y') }}
                                        </span>
                                    </span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
@endsection
