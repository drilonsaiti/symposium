<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="@yield('meta_description', 'Manage speaker bios, reusable talks and conference submissions.')"
    >

    <title>
        @hasSection('title')
            @yield('title') · {{ config('app.name', 'Symposium') }}
        @else
            {{ config('app.name', 'Symposium') }}
        @endif
    </title>

    @fonts

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    @stack('styles')
</head>

<body class="min-h-screen bg-gray-50 text-gray-950 antialiased">

@include('layouts.public.partials.navigation')

<main>
    @if(session('status'))
        <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                {{ session('status') }}
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div
                class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800">
                {{ $errors->first() }}
            </div>
        </div>
    @endif

    @yield('content')
</main>

@include('layouts.public.partials.footer')

@stack('scripts')
</body>
</html>
