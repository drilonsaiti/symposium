@php
    $workspaceUrl = \Illuminate\Support\Facades\Route::has('dashboard')
        ? route('dashboard')
        : (
            \Illuminate\Support\Facades\Route::has('talks.index')
                ? route('talks.index')
                : url('/dashboard')
        );

    $conferenceUrl = \Illuminate\Support\Facades\Route::has('public.conferences.index')
        ? route('public.conferences.index')
        : '#features';

    $guestPrimaryUrl = \Illuminate\Support\Facades\Route::has('register')
        ? route('register')
        : (
            \Illuminate\Support\Facades\Route::has('login')
                ? route('login')
                : '#features'
        );
@endphp

<header class="sticky top-0 z-50 border-b border-gray-200/80 bg-white/90 backdrop-blur-xl">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-6">

            <a
                href="{{ url('/') }}"
                class="flex shrink-0 items-center gap-3"
                aria-label="{{ config('app.name', 'Symposium') }} home"
            >
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-950 text-sm font-bold text-white shadow-sm">
                    S
                </span>

                <span class="text-lg font-bold tracking-tight text-gray-950">
                    {{ config('app.name', 'Symposium') }}
                </span>
            </a>

            <nav
                class="hidden items-center gap-1 md:flex"
                aria-label="Main navigation"
            >
                <a
                    href="{{ url('/#features') }}"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-950"
                >
                    Features
                </a>

                <a
                    href="{{ url('/#workflow') }}"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-950"
                >
                    Workflow
                </a>

                <a
                    href="{{ url('/#organizers') }}"
                    class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-950"
                >
                    For organizers
                </a>

                @if(\Illuminate\Support\Facades\Route::has('public.conferences.index'))
                    <a
                        href="{{ $conferenceUrl }}"
                        class="rounded-lg px-3 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-100 hover:text-gray-950"
                    >
                        Conferences
                    </a>
                @endif
            </nav>

            <div class="hidden items-center gap-3 md:flex">
                @auth
                    <x-notification-bell
                        :unread-count="$unreadCount ?? 0"
                        :latest-notifications="$latestNotifications ?? collect()"
                    />

                    <a
                        href="{{ $workspaceUrl }}"
                        class="inline-flex items-center justify-center rounded-xl bg-gray-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                    >
                        Open workspace
                    </a>
                @else
                    @if(\Illuminate\Support\Facades\Route::has('login'))
                        <a
                            href="{{ route('login') }}"
                            class="text-sm font-semibold text-gray-600 transition hover:text-gray-950"
                        >
                            Sign in
                        </a>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has('register'))
                        <a
                            href="{{ route('register') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-gray-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        >
                            Create account
                        </a>
                    @endif
                @endauth
            </div>

            <details class="group relative md:hidden">
                <summary
                    class="flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl border border-gray-300 bg-white text-gray-700 transition hover:bg-gray-50 hover:text-gray-950">
                    <span class="sr-only">Open navigation</span>

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

                <div class="absolute right-0 mt-3 w-72 rounded-2xl border border-gray-200 bg-white p-2 shadow-xl">
                    <nav
                        class="space-y-1"
                        aria-label="Mobile navigation"
                    >
                        <a
                            href="{{ url('/#features') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                        >
                            Features
                        </a>

                        <a
                            href="{{ url('/#workflow') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                        >
                            Workflow
                        </a>

                        <a
                            href="{{ url('/#organizers') }}"
                            class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                        >
                            For organizers
                        </a>

                        @if(\Illuminate\Support\Facades\Route::has('public.conferences.index'))
                            <a
                                href="{{ $conferenceUrl }}"
                                class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                            >
                                Conferences
                            </a>
                        @endif
                    </nav>

                    <div class="my-2 border-t border-gray-100"></div>

                    @auth
                        <a
                            href="{{ $workspaceUrl }}"
                            class="block rounded-xl bg-gray-950 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800"
                        >
                            Open workspace
                        </a>
                    @else
                        @if(\Illuminate\Support\Facades\Route::has('login'))
                            <a
                                href="{{ route('login') }}"
                                class="block rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                            >
                                Sign in
                            </a>
                        @endif

                        @if(\Illuminate\Support\Facades\Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="mt-1 block rounded-xl bg-gray-950 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-gray-800"
                            >
                                Create account
                            </a>
                        @endif
                    @endauth
                </div>
            </details>
        </div>
    </div>
</header>
