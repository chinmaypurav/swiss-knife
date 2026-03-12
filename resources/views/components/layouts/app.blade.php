<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Swiss Knife') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">
    <div class="navbar bg-base-100 shadow-sm">
        <div class="flex-1">
            <a href="/" class="btn btn-ghost text-xl">{{ config('app.name', 'Swiss Knife') }}</a>
        </div>
        <div class="flex-none">
            <ul class="menu menu-horizontal px-1">
                <li><a href="{{ route('s3.index') }}">S3 Files</a></li>
            </ul>
        </div>
    </div>

    <main class="container mx-auto p-4">
        {{ $slot }}
    </main>

    {{ $scripts ?? '' }}
</body>
</html>
