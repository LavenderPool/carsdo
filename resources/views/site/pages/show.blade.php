@extends('layouts.site')

@section('title', $page->title)

@section('content')
    <div class="block1">
        <h1>{{ $pageH1 ?? $page->title }}</h1>

        @if (filled($page->excerpt))
            <div class="p_test_drive">
                {{ $page->excerpt }}
            </div>
        @endif

        <article class="new_eq" style="margin-top:20px;">
            {!! $pageBodyHtml !!}
        </article>
    </div>
@endsection
