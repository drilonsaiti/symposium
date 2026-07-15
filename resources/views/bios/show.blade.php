@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:py-16">

            @if (session('success'))
                <div
                    class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            <a
                href="{{ route('bios.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 transition hover:text-gray-950"
            >
                <span aria-hidden="true">←</span>
                Back to bios
            </a>

            <article class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                <header class="border-b border-gray-200 p-7 sm:p-9">
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                Speaker bio
                            </p>

                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-950 sm:text-4xl">
                                {{ $bio->title }}
                            </h1>

                            <div class="mt-4 flex flex-wrap gap-2">
                                <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700">
                                    {{ $bio->type->label() }}
                                </span>

                                <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600">
                                    {{ str_word_count(strip_tags($bio->content)) }}
                                    {{ Str::plural('word', str_word_count(strip_tags($bio->content))) }}
                                </span>
                            </div>
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            @can('update', $bio)
                                <a
                                    href="{{ route('bios.edit', $bio) }}"
                                    class="rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                                >
                                    Edit bio
                                </a>
                            @endcan
                        </div>
                    </div>
                </header>

                <div class="space-y-8 p-7 sm:p-9">

                    @if ($bio->conferenceSubmission)
                        <section class="rounded-2xl bg-gray-50 p-5">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                Conference submission
                            </p>

                            <p class="mt-2 text-lg font-bold text-gray-950">
                                {{ $bio->conferenceSubmission->conference?->title
                                    ?? $bio->conferenceSubmission->conference?->name
                                    ?? $bio->conferenceSubmission->title
                                    ?? 'Submission #' . $bio->conferenceSubmission->id }}
                            </p>

                            @if ($bio->conferenceSubmission->status ?? false)
                                <p class="mt-2 text-sm font-medium text-gray-500">
                                    Status:
                                    {{ is_object($bio->conferenceSubmission->status)
                                        ? $bio->conferenceSubmission->status->label()
                                        : ucfirst($bio->conferenceSubmission->status) }}
                                </p>
                            @endif
                        </section>
                    @endif

                    <section>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Bio content
                        </p>

                        <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 p-5 sm:p-7">
                            <p class="whitespace-pre-line text-base leading-8 text-gray-800">{{ $bio->content }}</p>
                        </div>
                    </section>

                    <section class="border-t border-gray-200 pt-7">
                        <dl class="grid gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    Created
                                </dt>

                                <dd class="mt-2 font-semibold text-gray-900">
                                    {{ $bio->created_at->format('M d, Y H:i') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    Last updated
                                </dt>

                                <dd class="mt-2 font-semibold text-gray-900">
                                    {{ $bio->updated_at->format('M d, Y H:i') }}
                                </dd>
                            </div>
                        </dl>
                    </section>

                </div>

            </article>

        </div>
    </div>
@endsection
