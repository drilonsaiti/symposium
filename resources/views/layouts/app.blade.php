<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name', 'Symposium') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">

<nav class="bg-white shadow">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-6 py-4">
        <a href="{{ route('talks.index') }}"
           class="text-xl font-bold text-indigo-600">
            Symposium
        </a>

        <div class="flex items-center gap-4">
            <a href="{{ route('talks.index') }}"
               class="text-gray-600 hover:text-indigo-600">
                Talks
            </a>

            <a href="{{ route('talks.create') }}"
               class="rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                New Talk
            </a>
        </div>
    </div>
</nav>

<main class="mx-auto max-w-7xl px-6 py-8">
    @yield('content')
</main>

</body>
</html>
