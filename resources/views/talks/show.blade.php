@extends('layouts.app')

@section('content')
    <div class="min-h-screen">
        <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:py-16">

            @if (session('success') || session('status'))
                <div class="mb-7 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    {{ session('success') ?? session('status') }}
                </div>
            @endif

            <a
                href="{{ route('talks.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 transition hover:text-gray-950"
            >
                <span aria-hidden="true">←</span>
                Back to talks
            </a>

            @php
                $submissionGroups = $talk->conferences->groupBy(function ($conference) {
                    $status = $conference->pivot->status;

                    return $status instanceof \BackedEnum
                        ? $status->value
                        : (string) $status;
                });

                $nextConference = $talk->conferences
                    ->filter(function ($conference) {
                        return $conference->starts_at
                            && $conference->starts_at
                                ->copy()
                                ->startOfDay()
                                ->gte(now()->startOfDay());
                    })
                    ->sortBy('starts_at')
                    ->first();
            @endphp

            <article class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                <header class="border-b border-gray-200 p-7 sm:p-9">
                    <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">

                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                                Talk proposal
                            </p>

                            <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-950 sm:text-4xl">
                                {{ $talk->title }}
                            </h1>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700">
                                    {{ $talk->type->label() }}
                                </span>

                                <span class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600">
                                    {{ $talk->length }} minutes
                                </span>
                            </div>

                            @if ($talk->tags->isNotEmpty())
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($talk->tags as $tag)
                                        <a
                                            href="{{ route('talks.index', ['tag' => $tag->slug]) }}"
                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 transition hover:bg-gray-200"
                                        >
                                            {{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex shrink-0 flex-wrap items-center gap-3">
                            @can('update', $talk)
                                <a
                                    href="{{ route('talks.edit', $talk) }}"
                                    class="inline-flex items-center justify-center rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                                >
                                    Edit talk
                                </a>
                            @endcan

                            @can('delete', $talk)
                                <form
                                    action="{{ route('talks.destroy', $talk) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this talk? This action cannot be undone.')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="inline-flex items-center justify-center rounded-xl border border-red-200 bg-white px-5 py-3 text-sm font-semibold text-red-700 transition hover:bg-red-50"
                                    >
                                        Delete
                                    </button>
                                </form>
                            @endcan
                        </div>

                    </div>
                </header>

                <div class="space-y-9 p-7 sm:p-9">

                    <section>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Abstract
                        </p>

                        <div class="mt-4 rounded-2xl border border-gray-200 bg-gray-50 p-5 sm:p-7">
                            <p class="whitespace-pre-line text-base leading-8 text-gray-800">
                                {{ $talk->currentRevision->abstract ?? '' }}
                            </p>
                        </div>
                    </section>

                    <section>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Organizer notes
                        </p>

                        @if ($talk->organizer_notes)
                            <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-5 sm:p-7">
                                <p class="whitespace-pre-line leading-7 text-gray-700">
                                    {{ $talk->organizer_notes }}
                                </p>
                            </div>
                        @else
                            <div class="mt-4 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-5 py-6">
                                <p class="text-sm font-medium text-gray-500">
                                    No organizer notes have been added.
                                </p>
                            </div>
                        @endif
                    </section>

                    <section class="border-t border-gray-200 pt-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Talk details
                        </p>

                        <dl class="mt-5 grid gap-6 sm:grid-cols-2">

                            <div class="rounded-2xl bg-gray-50 p-5">
                                <dt class="text-sm font-medium text-gray-500">
                                    Format
                                </dt>

                                <dd class="mt-2 text-lg font-semibold text-gray-950">
                                    {{ $talk->type->label() }}
                                </dd>
                            </div>

                            <div class="rounded-2xl bg-gray-50 p-5">
                                <dt class="text-sm font-medium text-gray-500">
                                    Duration
                                </dt>

                                <dd class="mt-2 text-lg font-semibold text-gray-950">
                                    {{ $talk->length }} minutes
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    Created
                                </dt>

                                <dd class="mt-2 font-semibold text-gray-900">
                                    {{ $talk->created_at->format('M d, Y H:i') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    Last updated
                                </dt>

                                <dd class="mt-2 font-semibold text-gray-900">
                                    {{ $talk->updated_at->format('M d, Y H:i') }}
                                </dd>
                            </div>

                        </dl>
                    </section>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    Submission activity
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-950">
                                    @if ($talk->conferences_count === 0)
                                        Not submitted to any conference
                                    @else
                                        Submitted to {{ $talk->conferences_count }}
                                        {{ Str::plural('conference', $talk->conferences_count) }}
                                    @endif
                                </p>
                            </div>

                            @if ($talk->conferences_count === 0 && $isAuthor)
                                <a
                                    href="{{ route('public.conferences.index') }}"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 transition hover:text-gray-950"
                                >
                                    Find a conference
                                    <span aria-hidden="true">→</span>
                                </a>
                            @endif

                        </div>

                        @if ($talk->conferences_count > 0)

                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($submissionGroups as $statusValue => $conferences)
                                    @php
                                        $status = $conferences->first()->pivot->status;

                                        $statusLabel = is_object($status) && method_exists($status, 'label')
                                            ? $status->label()
                                            : Str::headline($statusValue);

                                        $statusClasses = match ($statusValue) {
                                            'accepted' =>
                                                'bg-emerald-50 text-emerald-700 ring-emerald-200',

                                            'rejected', 'declined' =>
                                                'bg-red-50 text-red-700 ring-red-200',

                                            'pending', 'submitted', 'under_review', 'reviewing' =>
                                                'bg-amber-50 text-amber-700 ring-amber-200',

                                            default =>
                                                'bg-white text-gray-700 ring-gray-200',
                                        };
                                    @endphp

                                    <span
                                        class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold ring-1 ring-inset {{ $statusClasses }}"
                                    >
                                        {{ $conferences->count() }}
                                        {{ $statusLabel }}
                                    </span>
                                @endforeach
                            </div>

                            @if ($nextConference)
                                <div class="mt-4 flex items-start gap-3 border-t border-gray-200 pt-4">

                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-gray-600 shadow-sm">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            class="h-5 w-5"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M8 2v3m8-3v3M4 9h16M5 4.5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1v-14a1 1 0 011-1z"
                                            />
                                        </svg>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-xs font-medium text-gray-500">
                                            Next submitted conference
                                        </p>

                                        <a
                                            href="{{ $isAuthor
                                                ? route('public.conferences.show', $nextConference)
                                                : route('conferences.show', $nextConference)
                                            }}"
                                            class="mt-1 block truncate text-sm font-semibold text-gray-900 hover:underline hover:underline-offset-2"
                                        >
                                            {{ $nextConference->title }}
                                        </a>

                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $nextConference->starts_at->format('M d, Y') }}
                                        </p>
                                    </div>

                                </div>
                            @endif

                        @endif
                    </div>

                    @if ($conferenceSubmissions->isNotEmpty())
                        <section class="border-t border-gray-200 pt-8">

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                    Conference submissions
                                </p>

                                <h2 class="mt-2 text-xl font-bold text-gray-950">
                                    Submission details
                                </h2>

                                <p class="mt-2 text-sm leading-6 text-gray-500">
                                    Review the information submitted to each conference.
                                </p>
                            </div>

                            <div
                                class="mt-6 space-y-3"
                                x-data="{ openSubmission: null }"
                            >
                                @foreach ($conferenceSubmissions as $submission)
                                    @php
                                        $status = $submission->status;

                                        $statusValue = $status instanceof \BackedEnum
                                            ? $status->value
                                            : (string) $status;

                                        $statusLabel = is_object($status) && method_exists($status, 'label')
                                            ? $status->label()
                                            : Str::headline($statusValue);

                                        $statusClasses = match ($statusValue) {
                                            'accepted' =>
                                                'bg-emerald-50 text-emerald-700 ring-emerald-200',

                                            'rejected', 'declined' =>
                                                'bg-red-50 text-red-700 ring-red-200',

                                            'pending', 'submitted', 'under_review', 'reviewing' =>
                                                'bg-amber-50 text-amber-700 ring-amber-200',

                                            default =>
                                                'bg-gray-100 text-gray-700 ring-gray-200',
                                        };
                                    @endphp

                                    <article class="overflow-hidden rounded-2xl border border-gray-200 bg-white">

                                        <button
                                            type="button"
                                            class="flex w-full items-center justify-between gap-4 px-5 py-5 text-left transition hover:bg-gray-50 sm:px-6"
                                            @click="openSubmission = openSubmission === {{ $submission->id }}
                                                ? null
                                                : {{ $submission->id }}"
                                            :aria-expanded="openSubmission === {{ $submission->id }}"
                                        >
                                            <div class="min-w-0">

                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h3 class="truncate font-bold text-gray-950">
                                                        {{ $submission->conference->title }}
                                                    </h3>

                                                    <span
                                                        class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClasses }}"
                                                    >
                                                        {{ $statusLabel }}
                                                    </span>
                                                </div>

                                                <div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500">
                                                    <span>
                                                        Submitted {{ $submission->created_at->format('M d, Y H:i') }}
                                                    </span>

                                                    <span>
                                                        {{ $submission->answers->count() }}
                                                        {{ Str::plural('answer', $submission->answers->count()) }}
                                                    </span>
                                                </div>

                                            </div>

                                            <svg
                                                class="h-5 w-5 shrink-0 text-gray-400 transition-transform"
                                                :class="{
                                                    'rotate-180':
                                                        openSubmission === {{ $submission->id }}
                                                }"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>

                                        <div
                                            x-show="openSubmission === {{ $submission->id }}"
                                            x-collapse
                                            x-cloak
                                            class="border-t border-gray-200"
                                        >

                                            <div class="flex items-center justify-between gap-4 bg-gray-50 px-5 py-3 sm:px-6">

                                                <span class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                                    Submission answers
                                                </span>

                                                <a
                                                    href="{{ $isAuthor
                                                        ? route('public.conferences.show', $submission->conference)
                                                        : route('conferences.show', $submission->conference)
                                                    }}"
                                                    class="shrink-0 text-xs font-semibold text-gray-600 transition hover:text-gray-950"
                                                >
                                                    View conference →
                                                </a>

                                            </div>

                                            @if ($submission->answers->isNotEmpty())
                                                <dl class="divide-y divide-gray-100">

                                                    @foreach ($submission->answers as $answer)
                                                        @if ($answer->question)
                                                            <div class="px-5 py-5 sm:px-6">

                                                                <dt class="text-sm font-semibold leading-6 text-gray-900">
                                                                    {{ $answer->question->question }}
                                                                </dt>

                                                                <dd class="mt-1 whitespace-pre-line text-sm leading-6 text-gray-600">
                                                                    {{ $answer->answer }}
                                                                </dd>

                                                            </div>
                                                        @endif
                                                    @endforeach

                                                </dl>
                                            @else
                                                <div class="px-5 py-5 sm:px-6">
                                                    <p class="text-sm text-gray-500">
                                                        No additional questions were answered for this submission.
                                                    </p>
                                                </div>
                                            @endif

                                        </div>

                                    </article>
                                @endforeach
                            </div>

                            @if ($conferenceSubmissions->hasPages())
                                <div class="mt-6">
                                    {{ $conferenceSubmissions->links() }}
                                </div>
                            @endif

                        </section>
                    @endif

                </div>

            </article>

        </div>
    </div>
@endsection
