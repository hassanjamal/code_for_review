<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    @if (app()->environment('local'))
        <link href="{{ mix('css/app.css') }}" rel="stylesheet"/>
        <script src="{{ mix('js/app.js') }}" defer></script>
    @else
        <link href="{{ global_asset('css/app.css')}}" rel="stylesheet">
        <script src="{{ global_asset('js/app.js') }}" defer></script>
    @endif

    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.0.1/dist/alpine.js" defer></script>

    @include('components.stripe')
    @routes
</head>
<body>
    @inertia
</body>
</html>
