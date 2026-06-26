@extends('layouts.site')

@section('title', $article->title)

@section('content')
    <div class="block1">
        <div class="hleb"><a href="/blog/">Блог</a></div>

        <h1>{{ $pageH1 ?? $article->title }}</h1>

        <div class="p_test_drive">
            @if ($article->published_at)
                {{ $article->published_at->translatedFormat('d F Y') }}
            @endif
            @if (filled($article->excerpt))
                @if ($article->published_at)
                    ·
                @endif
                {{ $article->excerpt }}
            @endif
        </div>

        <div style="margin:20px 0;">
            <img
                src="{{ $article->coverUrl() }}"
                alt="{{ $article->title }}"
                style="max-width:100%;height:auto;border-radius:10px;"
            >
        </div>

        <article class="new_eq" style="margin-top:20px;">
            {!! $articleBodyHtml !!}
        </article>
    </div>
@endsection
