@extends('layouts.app')

@section('content')
    <div class="min-h-screen ">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:py-16">

            @if (session('success'))
                <div
                    class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        Speaker profile
                    </p>

                    <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-950">
                        Speaker bios
                    </h1>

                    <p class="mt-3 max-w-2xl leading-7 text-gray-600">
                        Manage bio versions for conference websites, keynote introductions, formal profiles, and
                        community events.
                    </p>
                </div>

                @can('create', App\Models\Bio::class)
                    <a
                        href="{{ route('bios.create') }}"
                        class="inline-flex shrink-0 items-center justify-center rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                    >
                        Create bio
                    </a>
                @endcan
            </div>

            @if ($bios->isEmpty())
                <section
                    class="mt-8 rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-xl text-gray-700">
                        Aa
                    </div>

                    <h2 class="mt-5 text-xl font-bold text-gray-950">
                        No speaker bios yet
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                        Create separate bio versions for different events, audiences, and submission requirements.
                    </p>

                    @can('create', App\Models\Bio::class)
                        <a
                            href="{{ route('bios.create') }}"
                            class="mt-6 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        >
                            Create your first bio
                        </a>
                    @endcan
                </section>
            @else
                <div class="mt-8 grid gap-5 lg:grid-cols-2">
                    @foreach ($bios as $bio)
                        <article
                            class="flex flex-col rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-gray-300 hover:shadow-md sm:p-7">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <a href="{{ route('bios.show', $bio) }}">
                                        <h2 class="text-xl font-bold text-gray-950 transition hover:text-gray-600">
                                            {{ $bio->title }}
                                        </h2>
                                    </a>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span
                                            class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700">
                                            {{ $bio->nickname }}
                                        </span>

                                        <span
                                            class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600">
                                            {{ str_word_count(strip_tags($bio->bio)) }}
                                            {{ Str::plural('word', str_word_count(strip_tags($bio->bio))) }}
                                        </span>
                                    </div>
                                </div>

                                <p class="shrink-0 text-sm font-medium text-gray-400">
                                    {{ $bio->updated_at->diffForHumans() }}
                                </p>
                            </div>

                            @if ($bio->conferenceSubmission)
                                <div class="mt-5 rounded-2xl bg-gray-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                        Conference submission
                                    </p>

                                    <p class="mt-2 font-semibold text-gray-900">
                                        {{ $bio->conferenceSubmission->conference?->title
                                            ?? $bio->conferenceSubmission->conference?->name
                                            ?? $bio->conferenceSubmission->title
                                            ?? 'Submission #' . $bio->conferenceSubmission->id }}
                                    </p>
                                </div>
                            @endif

                            <p class="mt-5 flex-1 leading-7 text-gray-600">
                                {{ Str::limit($bio->bio, 240) }}
                            </p>

                            <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-5">
                                <a
                                    href="{{ route('bios.show', $bio) }}"
                                    class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                                >
                                    View
                                </a>

                                @can('update', $bio)
                                    <a
                                        href="{{ route('bios.edit', $bio) }}"
                                        class="rounded-xl bg-gray-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                                    >
                                        Edit
                                    </a>
                                @endcan
                            </div>

                        </article>
                    @endforeach
                </div>

                @if ($bios->hasPages())
                    <div class="mt-8">
                        {{ $bios->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
@endsection
