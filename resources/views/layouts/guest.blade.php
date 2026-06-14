<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PostSaja') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-background">
            <div class="mb-8 text-center">
                <a href="{{ config('app.url', '/') }}" class="text-2xl font-extrabold">
                    <span class="bg-gradient-brand bg-clip-text text-transparent">PostSaja</span>
                </a>
                <p class="text-sm text-muted mt-1">Marketing untuk yang tak sempat marketing</p>
            </div>

            <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-sm border border-border sm:rounded-xl">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
