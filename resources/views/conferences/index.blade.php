@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen">

        <div class="max-w-7xl mx-auto px-6 py-16">

            <!-- Header -->
            <div class="max-w-3xl mb-14">
                <p class="text-sm font-semibold tracking-widest uppercase text-gray-500 mb-3">
                    Events
                </p>

                <h1 class="text-5xl font-bold tracking-tight text-gray-900">
                    Discover conferences
                </h1>

                <p class="mt-5 text-lg text-gray-600 leading-relaxed">
                    Explore upcoming conferences, connect with communities,
                    and discover opportunities to share knowledge.
                </p>
            </div>


            @if($conferences->isEmpty())

                <div class="rounded-2xl border border-gray-200 bg-white p-12 text-center">
                    <h2 class="text-xl font-semibold text-gray-900">
                        No conferences yet
                    </h2>

                    <p class="mt-2 text-gray-500">
                        New events will appear here soon.
                    </p>
                </div>

            @else


                <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">

                    @foreach($conferences as $conference)

                        <article
                            class="group bg-white rounded-3xl border border-gray-200 overflow-hidden transition hover:-translate-y-1 hover:shadow-xl"
                        >

                            <!-- Top section -->
                            <div class="p-7 pb-5">

                                <div class="flex justify-between items-start gap-4">

                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 leading-snug group-hover:text-gray-700 transition">
                                            {{ $conference->title }}
                                        </h2>
                                    </div>


                                    @php
                                        $now = now()->startOfDay();
                                    @endphp


                                    @if($now->between($conference->cfp_starts_at, $conference->cfp_ends_at))

                                        <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                CFP Open
                            </span>

                                    @elseif($now->lt($conference->cfp_starts_at))

                                        <span class="shrink-0 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">
                                Upcoming
                            </span>

                                    @else

                                        <span class="shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                Closed
                            </span>

                                    @endif

                                </div>


                                <div class="mt-6 space-y-3 text-sm text-gray-600">


                                    <div class="flex items-center gap-3">

                                        <div class="h-9 w-9 rounded-xl bg-gray-100 flex items-center justify-center">
                                            📍
                                        </div>

                                        <span>
                                {{ $conference->location }}
                            </span>

                                    </div>


                                    <div class="flex items-center gap-3">

                                        <div class="h-9 w-9 rounded-xl bg-gray-100 flex items-center justify-center">
                                            📅
                                        </div>

                                        <span>
                                {{ $conference->starts_at->format('M d, Y') }}
                                -
                                {{ $conference->ends_at->format('M d, Y') }}
                            </span>

                                    </div>


                                </div>

                            </div>


                            <!-- Description -->
                            <div class="px-7 pb-6">

                                <p class="text-gray-600 leading-relaxed line-clamp-3">
                                    {{ $conference->description }}
                                </p>

                            </div>


                            <!-- CFP -->
                            <div class="mx-7 mb-7 rounded-2xl bg-gray-50 border border-gray-100 p-5">

                                <div class="flex justify-between items-center">

                                    <div>

                                        <p class="text-xs uppercase tracking-wide text-gray-500 font-semibold">
                                            Call for Papers
                                        </p>

                                        <p class="mt-1 text-sm font-medium text-gray-900">
                                            {{ $conference->cfp_starts_at->format('M d') }}
                                            -
                                            {{ $conference->cfp_ends_at->format('M d, Y') }}
                                        </p>

                                    </div>


                                    <svg class="w-6 h-6 text-gray-400"
                                         fill="none"
                                         stroke="currentColor"
                                         viewBox="0 0 24 24">

                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M9 5l7 7-7 7"/>

                                    </svg>

                                </div>

                            </div>


                            <!-- Footer -->
                            <div class="border-t border-gray-100 px-7 py-5">

                                <a
                                    href="{{ route('conferences.show', $conference) }}"
                                    class="flex items-center justify-between text-sm font-semibold text-gray-900 group-hover:text-black"
                                >

                        <span>
                            View conference
                        </span>


                                    <span class="transition group-hover:translate-x-1">
                            →
                        </span>

                                </a>

                            </div>


                        </article>

                    @endforeach

                </div>


                <div class="mt-12">
                    {{ $conferences->links() }}
                </div>


            @endif

        </div>

    </div>
@endsection
