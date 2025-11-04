<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Recipe Manager')</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="shortcut icon" href="/favicon.svg">
    {{-- Load CSS built by Vite (dev) and provide a public asset fallback for production --}}
    @if (app()->environment('local'))
        @vite('resources/css/app.css')
    @endif
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    @yield('body')
    @yield('scripts')
</body>
</html>
