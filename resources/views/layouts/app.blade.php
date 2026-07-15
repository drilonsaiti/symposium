<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', config('app.name', 'Symposium'))
    </title>

    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])

    @stack('head')
</head>

<body class="min-h-screen bg-gray-50 text-gray-950 antialiased">

<nav class="sticky top-0 z-50 border-b border-gray-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <div class="flex h-16 items-center justify-between gap-6">

            <a
                href="{{ route('dashboard') }}"
                class="flex shrink-0 items-center gap-3"
            >
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-950 text-sm font-bold text-white">
                        S
                    </span>

                <span class="text-lg font-bold tracking-tight text-gray-950">
                        Symposium
                    </span>
            </a>

            <div class="hidden flex-1 items-center justify-center gap-1 md:flex">

                <a
                    href="{{ route('dashboard') }}"
                    @class([
                        'rounded-lg px-3 py-2 text-sm font-semibold transition',
                        'bg-gray-100 text-gray-950' => request()->routeIs('dashboard'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-950' => !request()->routeIs('dashboard'),
                    ])
                >
                    Dashboard
                </a>

                <a
                    href="{{ route('talks.index') }}"
                    @class([
                        'rounded-lg px-3 py-2 text-sm font-semibold transition',
                        'bg-gray-100 text-gray-950' => request()->routeIs('talks.*'),
                        'text-gray-600 hover:bg-gray-50 hover:text-gray-950' => !request()->routeIs('talks.*'),
                    ])
                >
                    Talks
                </a>

                @if (\Illuminate\Support\Facades\Route::has('bios.index'))
                    <a
                        href="{{ route('bios.index') }}"
                        @class([
                            'rounded-lg px-3 py-2 text-sm font-semibold transition',
                            'bg-gray-100 text-gray-950' => request()->routeIs('bios.*'),
                            'text-gray-600 hover:bg-gray-50 hover:text-gray-950' => !request()->routeIs('bios.*'),
                        ])
                    >
                        Bios
                    </a>
                @endif

                @if (\Illuminate\Support\Facades\Route::has('conferences.index'))
                    <a
                        href="{{ route('conferences.index') }}"
                        @class([
                            'rounded-lg px-3 py-2 text-sm font-semibold transition',
                            'bg-gray-100 text-gray-950' => request()->routeIs('conferences.*'),
                            'text-gray-600 hover:bg-gray-50 hover:text-gray-950' => !request()->routeIs('conferences.*'),
                        ])
                    >
                        Conferences
                    </a>
                @endif

            </div>

            <div class="hidden shrink-0 items-center md:flex">
                @auth
                    <div class="group relative">

                        <button
                            type="button"
                            class="flex items-center gap-3 rounded-xl px-2 py-1.5 text-left transition hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                            aria-haspopup="true"
                        >
                                <span
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </span>

                            <span class="hidden lg:block">
                                    <span class="block max-w-40 truncate text-sm font-semibold text-gray-900">
                                        {{ auth()->user()->name }}
                                    </span>

                                    <span class="block text-xs text-gray-500">
                                        Account
                                    </span>
                                </span>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="h-4 w-4 text-gray-400 transition group-hover:rotate-180 group-focus-within:rotate-180"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.22 7.22a.75.75 0 011.06 0L10 10.94l3.72-3.72a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.22 8.28a.75.75 0 010-1.06z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>

                        <div class="absolute right-0 top-full h-3 w-full"></div>

                        <div
                            class="invisible absolute right-0 top-full z-50 mt-3 w-56 translate-y-1 rounded-2xl border border-gray-200 bg-white p-2 opacity-0 shadow-lg transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">

                            <div class="border-b border-gray-100 px-3 py-3">
                                <p class="truncate text-sm font-semibold text-gray-950">
                                    {{ auth()->user()->name }}
                                </p>

                                @if (auth()->user()->email)
                                    <p class="mt-1 truncate text-xs text-gray-500">
                                        {{ auth()->user()->email }}
                                    </p>
                                @endif
                            </div>

                            @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="mt-2 flex items-center rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                                >
                                    Profile
                                </a>
                            @endif

                            @if (\Illuminate\Support\Facades\Route::has('logout'))
                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                    class="mt-1"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="flex w-full items-center rounded-xl px-3 py-2.5 text-left text-sm font-semibold text-red-700 transition hover:bg-red-50"
                                    >
                                        Sign out
                                    </button>
                                </form>
                            @endif

                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        @if (\Illuminate\Support\Facades\Route::has('login'))
                            <a
                                href="{{ route('login') }}"
                                class="text-sm font-semibold text-gray-600 transition hover:text-gray-950"
                            >
                                Sign in
                            </a>
                        @endif

                        @if (\Illuminate\Support\Facades\Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="rounded-xl bg-gray-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                            >
                                Create account
                            </a>
                        @endif
                    </div>
                @endauth
            </div>

            <details class="group relative md:hidden">
                <summary
                    class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-700 transition hover:bg-gray-50 hover:text-gray-950">
                        <span class="sr-only">
                            Open navigation
                        </span>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-5 w-5 group-open:hidden"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="hidden h-5 w-5 group-open:block"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </summary>

                <div
                    class="absolute right-0 mt-3 w-72 overflow-hidden rounded-2xl border border-gray-200 bg-white p-2 shadow-lg">

                    @auth
                        <div class="border-b border-gray-100 px-3 py-3">
                            <div class="flex items-center gap-3">
                                    <span
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 text-sm font-bold text-gray-700">
                                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                    </span>

                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-gray-950">
                                        {{ auth()->user()->name }}
                                    </p>

                                    @if (auth()->user()->email)
                                        <p class="mt-0.5 truncate text-xs text-gray-500">
                                            {{ auth()->user()->email }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endauth

                    <div class="py-2">
                        <a
                            href="{{ route('talks.index') }}"
                            @class([
                                'block rounded-xl px-4 py-3 text-sm font-semibold transition',
                                'bg-gray-100 text-gray-950' => request()->routeIs('talks.*'),
                                'text-gray-600 hover:bg-gray-50 hover:text-gray-950' => !request()->routeIs('talks.*'),
                            ])
                        >
                            Talks
                        </a>

                        @if (\Illuminate\Support\Facades\Route::has('bios.index'))
                            <a
                                href="{{ route('bios.index') }}"
                                @class([
                                    'mt-1 block rounded-xl px-4 py-3 text-sm font-semibold transition',
                                    'bg-gray-100 text-gray-950' => request()->routeIs('bios.*'),
                                    'text-gray-600 hover:bg-gray-50 hover:text-gray-950' => !request()->routeIs('bios.*'),
                                ])
                            >
                                Bios
                            </a>
                        @endif

                        @if (\Illuminate\Support\Facades\Route::has('conferences.index'))
                            <a
                                href="{{ route('conferences.index') }}"
                                @class([
                                    'mt-1 block rounded-xl px-4 py-3 text-sm font-semibold transition',
                                    'bg-gray-100 text-gray-950' => request()->routeIs('conferences.*'),
                                    'text-gray-600 hover:bg-gray-50 hover:text-gray-950' => !request()->routeIs('conferences.*'),
                                ])
                            >
                                Conferences
                            </a>
                        @endif
                    </div>

                    <div class="border-t border-gray-100 pt-2">
                        @auth
                            @if (\Illuminate\Support\Facades\Route::has('profile.edit'))
                                <a
                                    href="{{ route('profile.edit') }}"
                                    class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                                >
                                    Profile
                                </a>
                            @endif

                            @if (\Illuminate\Support\Facades\Route::has('logout'))
                                <form
                                    method="POST"
                                    action="{{ route('logout') }}"
                                    class="mt-1"
                                >
                                    @csrf

                                    <button
                                        type="submit"
                                        class="w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-red-700 transition hover:bg-red-50"
                                    >
                                        Sign out
                                    </button>
                                </form>
                            @endif
                        @else
                            @if (\Illuminate\Support\Facades\Route::has('login'))
                                <a
                                    href="{{ route('login') }}"
                                    class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                                >
                                    Sign in
                                </a>
                            @endif

                            @if (\Illuminate\Support\Facades\Route::has('register'))
                                <a
                                    href="{{ route('register') }}"
                                    class="mt-1 block rounded-xl bg-gray-950 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800"
                                >
                                    Create account
                                </a>
                            @endif
                        @endauth
                    </div>

                </div>
            </details>

        </div>
    </div>
</nav>

<main>
    @yield('content')
</main>

<footer class="border-t border-gray-200 bg-white">
    <div
        class="mx-auto flex max-w-7xl flex-col gap-2 px-4 py-6 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
        <p>
            &copy; {{ now()->year }} Symposium
        </p>

        <p>
            Speaker and conference management
        </p>
    </div>
</footer>

@stack('scripts')
</body>
</html>

