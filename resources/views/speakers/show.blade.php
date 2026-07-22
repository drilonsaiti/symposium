@extends('layouts.public.app')

@section('title', $speaker->name . ' · Speaker Profile')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-16 sm:px-6 lg:px-8">

        <div class="mb-14 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-3xl bg-gray-950 text-2xl font-bold text-white shadow-lg shadow-gray-950/10">
                {{ strtoupper(substr($speaker->name, 0, 1)) }}
            </div>

            <h1 class="mt-6 text-4xl font-bold tracking-tight text-gray-950">
                {{ $speaker->name }}
            </h1>

            @if($speaker->username)
                <p class="mt-2 inline-flex items-center gap-1 text-sm font-medium text-gray-500">
                    <span class="text-gray-400">@</span>{{ $speaker->username }}
                </p>
            @endif

            <div class="mx-auto mt-8 flex max-w-xs items-center justify-center divide-x divide-gray-200 rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex-1 px-6 py-4 text-center">
                    <p class="text-2xl font-bold tracking-tight text-gray-950">{{ $stats['accepted_talks'] }}</p>
                    <p class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-gray-400">
                        {{ Str::plural('Talk', $stats['accepted_talks']) }}
                    </p>
                </div>
                <div class="flex-1 px-6 py-4 text-center">
                    <p class="text-2xl font-bold tracking-tight text-gray-950">{{ $stats['conferences_spoken_at'] }}</p>
                    <p class="mt-0.5 text-xs font-semibold uppercase tracking-wide text-gray-400">
                        {{ Str::plural('Conference', $stats['conferences_spoken_at']) }}
                    </p>
                </div>
            </div>
        </div>

        @if($acceptedTalks->isNotEmpty())
            <section class="mb-14">
                <div class="mb-5 flex items-center gap-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-gray-950">
                        Accepted talks
                    </h2>
                    <span class="h-px flex-1 bg-gray-200"></span>
                </div>

                <div class="space-y-4">
                    @foreach($acceptedTalks as $talk)
                        @foreach($talk->conferences as $conference)
                            <article class="group overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-xl shadow-gray-950/5 transition hover:shadow-2xl hover:shadow-gray-950/10">
                                <div class="border-l-4 border-emerald-500 p-6 sm:p-7">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="flex items-center gap-2 text-sm font-semibold text-gray-500">
                                                {{ $conference->title }}

                                                @if($conference->starts_at)
                                                    <span class="text-gray-300">·</span>
                                                    <span class="text-gray-400">{{ $conference->starts_at->format('M Y') }}</span>
                                                @endif
                                            </p>

                                            <h3 class="mt-1.5 text-xl font-bold tracking-tight text-gray-950">
                                                {{ $talk->title }}
                                            </h3>
                                        </div>

                                        <span class="inline-flex shrink-0 items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                            Accepted
                                        </span>
                                    </div>

                                    @if($conference->pivot->bio ?? null)
                                        <div class="mt-5 border-t border-gray-100 pt-5">
                                            <p class="mb-1.5 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                                Speaker bio
                                            </p>
                                            <p class="text-sm leading-6 text-gray-600">
                                                {{ $conference->pivot->bio->bio }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    @endforeach
                </div>
            </section>
        @endif

        @if($availableTalks->isNotEmpty())
            <section>
                <div class="mb-5 flex items-center gap-3">
                    <h2 class="text-sm font-bold uppercase tracking-wide text-gray-950">
                        Available to pitch
                    </h2>
                    <span class="h-px flex-1 bg-gray-200"></span>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach($availableTalks as $talk)
                        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl shadow-gray-950/5 transition hover:shadow-2xl hover:shadow-gray-950/10">
                            <h3 class="text-base font-bold tracking-tight text-gray-950">
                                {{ $talk->title }}
                            </h3>

                            @if($talk->currentRevision)
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-gray-600">
                                    {{ $talk->currentRevision->abstract }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if($acceptedTalks->isEmpty() && $availableTalks->isEmpty())
            <div class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center">
                <p class="text-sm font-medium text-gray-500">
                    {{ $speaker->name }} hasn't published any talks yet. Check back soon.
                </p>
            </div>
        @endif

    </div>
@endsection
