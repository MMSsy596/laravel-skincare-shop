<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-body">
        <div class="auth-shell">
            <div class="text-center mb-4">
                <a href="/" class="d-inline-flex align-items-center justify-content-center gap-2 text-decoration-none">
                    <x-application-logo class="w-16 h-16" />
                    <span class="fw-bold fs-4 text-dark">BeautyAI</span>
                </a>
            </div>
            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
