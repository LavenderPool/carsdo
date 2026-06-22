<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {!! seo($SEOData ?? null) !!}

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto+Condensed" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/global-styles.css') }}">
        @stack('head')
    </head>
    <body>
        <div class="zero">
            @include('layouts.header')
            @yield('content')
            @include('layouts.footer')
        </div>
    </body>
</html>
