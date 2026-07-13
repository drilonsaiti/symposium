@extends('layouts.app')

@section('content')
    <div class="bg-gray-50 min-h-screen">

        <div class="max-w-6xl mx-auto px-6 py-16">


            <!-- Hero -->
            <section class="bg-white border border-gray-200 rounded-3xl overflow-hidden">

                <div class="p-8 md:p-12">

                    <div class="max-w-4xl">

                        <p class="text-sm uppercase tracking-widest font-semibold text-gray-500 mb-5">
                            Conference
                        </p>


                        <h1 class="text-5xl md:text-6xl font-bold tracking-tight text-gray-900 leading-tight">
                            {{ $conference->title }}
                        </h1>


                        <div class="mt-8 flex flex-wrap gap-6 text-gray-600">


                            <div class="flex items-center gap-3">

                                <div class="h-11 w-11 rounded-2xl bg-gray-100 flex items-center justify-center">
                                    📍
                                </div>

                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">
                                        Location
                                    </p>

                                    <p class="font-medium text-gray-900">
                                        {{ $conference->location }}
                                    </p>
                                </div>

                            </div>



                            <div class="flex items-center gap-3">

                                <div class="h-11 w-11 rounded-2xl bg-gray-100 flex items-center justify-center">
                                    📅
                                </div>

                                <div>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">
                                        Date
                                    </p>

                                    <p class="font-medium text-gray-900">
                                        {{ $conference->starts_at->format('M d, Y') }}
                                        -
                                        {{ $conference->ends_at->format('M d, Y') }}
                                    </p>
                                </div>

                            </div>


                        </div>

                    </div>

                </div>


                <!-- Divider -->
                <div class="border-t border-gray-100"></div>


                <!-- Description -->
                <div class="p-8 md:p-12">

                    <h2 class="text-2xl font-bold text-gray-900 mb-5">
                        About this conference
                    </h2>


                    <p class="text-lg text-gray-600 leading-relaxed whitespace-pre-line max-w-4xl">
                        {{ $conference->description }}
                    </p>

                </div>


            </section>



            <div class="grid md:grid-cols-3 gap-8 mt-8">


                <!-- CFP -->
                <section class="md:col-span-2 bg-white border border-gray-200 rounded-3xl p-8">


                    @php
                        $now = now()->startOfDay();
                    @endphp


                    <div class="flex justify-between items-start mb-8">

                        <div>
                            <p class="text-sm uppercase tracking-widest text-gray-500 font-semibold">
                                Call for Papers
                            </p>

                            <h2 class="mt-2 text-2xl font-bold text-gray-900">
                                Submit your proposal
                            </h2>
                        </div>


                        @if($now->between($conference->cfp_starts_at, $conference->cfp_ends_at))

                            <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                            Open
                        </span>

                        @elseif($now->lt($conference->cfp_starts_at))

                            <span class="rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700">
                            Upcoming
                        </span>

                        @else

                            <span class="rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-600">
                            Closed
                        </span>

                        @endif

                    </div>



                    <div class="grid sm:grid-cols-2 gap-6">


                        <div class="rounded-2xl bg-gray-50 p-5">

                            <p class="text-sm text-gray-500">
                                Opens
                            </p>

                            <p class="mt-2 text-lg font-semibold text-gray-900">
                                {{ $conference->cfp_starts_at->format('F d, Y') }}
                            </p>

                        </div>



                        <div class="rounded-2xl bg-gray-50 p-5">

                            <p class="text-sm text-gray-500">
                                Deadline
                            </p>

                            <p class="mt-2 text-lg font-semibold text-gray-900">
                                {{ $conference->cfp_ends_at->format('F d, Y') }}
                            </p>

                        </div>


                    </div>


                </section>



                <!-- Sidebar -->
                <aside class="space-y-8">


                    @if($conference->url)

                        <div class="bg-white border border-gray-200 rounded-3xl p-8">

                            <h3 class="font-bold text-xl text-gray-900">
                                Official website
                            </h3>

                            <p class="mt-3 text-gray-600 text-sm">
                                Find more information, schedule and registration details.
                            </p>


                            <a
                                href="{{ $conference->url }}"
                                target="_blank"
                                class="mt-6 flex items-center justify-center gap-2 rounded-xl bg-gray-900 px-5 py-3 text-white font-semibold hover:bg-gray-800 transition"
                            >
                                Visit website
                                →
                            </a>

                        </div>

                    @endif



                    <div class="bg-white border border-gray-200 rounded-3xl p-8">

                        <p class="text-sm uppercase tracking-widest text-gray-500">
                            Organizer
                        </p>

                        <p class="mt-3 text-xl font-bold text-gray-900">
                            {{ $conference->user->name }}
                        </p>

                    </div>


                </aside>


            </div>


        </div>

    </div>
@endsection
