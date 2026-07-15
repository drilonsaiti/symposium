@extends('layouts.app')

@section('title', 'Conferences')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-16">

            @if (session('success'))
                <div
                    class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <header class="mb-10 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between lg:mb-12">
                <div class="max-w-3xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        Events
                    </p>

                    <h1 class="mt-3 text-4xl font-bold tracking-tight text-gray-950 sm:text-5xl">
                        Discover conferences
                    </h1>

                    <p class="mt-4 max-w-2xl text-base leading-7 text-gray-600 sm:text-lg sm:leading-8">
                        Explore upcoming conferences, connect with communities,
                        and discover opportunities to share your knowledge.
                    </p>
                </div>

                @can('create', App\Models\Conference::class)
                    <a
                        href="{{ route('conferences.create') }}"
                        class="inline-flex w-full shrink-0 items-center justify-center rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 sm:w-auto"
                    >
                        Create conference
                    </a>
                @endcan
            </header>

            <form method="GET"
                  id="conference-filter-form"
                  action="{{ route('conferences.index') }}"
                  class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">

                <div class="flex flex-col gap-4 lg:flex-row lg:items-center">

                    <div class="relative flex-1">


                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"
                             class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400">
                            <path
                                d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376C296.3 401.1 253.9 416 208 416 93.1 416 0 322.9 0 208S93.1 0 208 0 416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                        </svg>

                        <input
                            id="search-input"
                            type="text"
                            name="term"
                            value="{{ request('term') }}"
                            placeholder="Search conferences..."
                            class="w-full rounded-xl border-gray-300 py-3 pl-11 pr-4 text-sm focus:border-gray-900 focus:ring-gray-900">
                    </div>

                    <select
                        id="conference-date"
                        name="conference_date"
                        class="appearance-none rounded-xl border-gray-300 py-3 pl-4 pr-10 text-sm leading-5 lg:w-48"
                    >
                        <option value="">All conferences</option>
                        <option value="upcoming" @selected(request('conference_date') === 'upcoming')>
                            Upcoming
                        </option>
                        <option value="past" @selected(request('conference_date') === 'past')>
                            Past
                        </option>
                    </select>

                    <select
                        id="cfp_status"
                        name="cfp_status"
                        class="rounded-xl border-gray-300 px-4 py-3 text-sm lg:w-40">

                        <option value="">All CFPs</option>
                        <option value="open" @selected(request('cfp_status') === 'open')>
                            Open
                        </option>
                        <option value="upcoming" @selected(request('cfp_status') === 'upcoming')>Upcoming</option>
                        <option value="closed" @selected(request('cfp_status') === 'closed')>Closed</option>

                    </select>

                    <a
                        href="{{ route('public.conferences.index') }}"
                        class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 self-end">
                        Reset
                    </a>

                </div>

            </form>


            <div id="conference-list">
                @include('conferences.public.partials.list')
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('conference-filter-form');
            const list = document.getElementById('conference-list');

            async function loadConferences() {
                const params = new URLSearchParams(new FormData(form));

                const query = params.toString();

                history.replaceState(
                    {},
                    '',
                    query ? `?${query}` : window.location.pathname
                );

                const response = await fetch(`{{ route('public.conferences.index') }}?${params}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                list.innerHTML = await response.text();
            }

            document.getElementById('conference-date')
                .addEventListener('change', loadConferences);

            document.getElementById('cfp_status')
                .addEventListener('change', loadConferences);

            let timeout;

            document.getElementById('search-input')
                .addEventListener('input', () => {
                    clearTimeout(timeout);

                    timeout = setTimeout(loadConferences, 300);
                })
        });
    </script>
@endpush
