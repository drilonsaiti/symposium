@extends('layouts.public.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:py-16">

            @if(session('status'))
                <div
                    class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div
                    class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-800">
                    {{ $errors->first() }}
                </div>
            @endif

            <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                <div class="p-7 sm:p-9 lg:p-12">
                    <div class="max-w-4xl">
                        <p class="mb-4 text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                            Conference
                        </p>

                        <h1 class="text-4xl font-bold leading-tight tracking-tight text-gray-950 sm:text-5xl lg:text-6xl">
                            {{ $conference->title }}
                        </h1>

                        <div class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="flex items-center gap-4 rounded-2xl bg-gray-50 p-4">
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm"
                                    aria-hidden="true">
                                    📍
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Location
                                    </p>
                                    <p class="mt-1 truncate font-semibold text-gray-900">
                                        {{ $conference->location }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 rounded-2xl bg-gray-50 p-4">
                                <div
                                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white shadow-sm"
                                    aria-hidden="true">
                                    📅
                                </div>

                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                        Date
                                    </p>
                                    <p class="mt-1 font-semibold text-gray-900">
                                        {{ $conference->starts_at->format('M d, Y') }}
                                        <span class="text-gray-400">to</span>
                                        {{ $conference->ends_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section
                class="mt-8 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm"
                data-conference-tabs
            >
                <div class="overflow-x-auto border-b border-gray-200">
                    <div
                        class="flex min-w-max gap-1 px-4 sm:px-6"
                        role="tablist"
                        aria-label="Conference information"
                    >
                        <button
                            type="button"
                            id="tab-details"
                            class="border-b-2 border-gray-950 px-4 py-5 text-sm font-semibold text-gray-950 transition"
                            role="tab"
                            aria-selected="true"
                            aria-controls="panel-details"
                            tabindex="0"
                            data-tab-button="details"
                        >
                            Details
                        </button>

                        <button
                            type="button"
                            id="tab-speakers"
                            class="border-b-2 border-transparent px-4 py-5 text-sm font-semibold text-gray-500 transition hover:text-gray-950"
                            role="tab"
                            aria-selected="false"
                            aria-controls="panel-speakers"
                            tabindex="-1"
                            data-tab-button="speakers"
                        >
                            Speakers
                        </button>

                        @can('viewSubmissions', $conference)
                            <button
                                type="button"
                                id="tab-submissions"
                                class="border-b-2 border-transparent px-4 py-5 text-sm font-semibold text-gray-500 transition hover:text-gray-950"
                                role="tab"
                                aria-selected="false"
                                aria-controls="panel-submissions"
                                tabindex="-1"
                                data-tab-button="submissions"
                            >
                                Talk submissions
                            </button>
                        @endcan
                    </div>
                </div>

                <div
                    id="panel-details"
                    class="p-7 sm:p-9"
                    role="tabpanel"
                    aria-labelledby="tab-details"
                    data-tab-panel="details"
                >
                    <div class="max-w-4xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Overview
                        </p>
                        <h2 class="mt-2 text-2xl font-bold text-gray-950">
                            About this conference
                        </h2>
                        <p class="mt-5 whitespace-pre-line text-base leading-8 text-gray-600">
                            {{ $conference->description }}
                        </p>
                    </div>
                </div>

                <div
                    id="panel-speakers"
                    class="p-7 sm:p-9"
                    role="tabpanel"
                    aria-labelledby="tab-speakers"
                    data-tab-panel="speakers"
                    hidden
                >
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                Programme
                            </p>
                            <h2 class="mt-2 text-2xl font-bold text-gray-950">
                                Speakers and talks
                            </h2>
                        </div>

                        @unless($acceptedTalks->isEmpty())
                            <p class="text-sm font-medium text-gray-500">
                                {{ $acceptedTalks->count() }} {{ Str::plural('talk', $acceptedTalks->count()) }}
                            </p>
                        @endunless
                    </div>

                    @if($acceptedTalks->isEmpty())
                        <div
                            class="mt-7 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center">
                            <p class="font-semibold text-gray-900">Speakers will be announced soon.</p>
                            <p class="mt-1 text-sm text-gray-500">Check back later for programme updates.</p>
                        </div>
                    @else
                        <div class="mt-7 grid gap-4">
                            @foreach($acceptedTalks as $talk)
                                <article
                                    class="rounded-2xl border border-gray-200 p-5 transition hover:border-gray-300 hover:shadow-sm sm:p-6">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0">
                                            <h3 class="text-lg font-bold text-gray-950 sm:text-xl">
                                                {{ $talk->title }}
                                            </h3>
                                            <p class="mt-2 leading-7 text-gray-600">
                                                {{ $talk->abstract }}
                                            </p>
                                        </div>

                                        <div class="flex shrink-0 flex-wrap gap-2 text-xs font-semibold text-gray-600">
                                            <span class="rounded-full bg-gray-100 px-3 py-1.5">
                                                {{ $talk->author->name }}
                                            </span>
                                            <span class="rounded-full bg-gray-100 px-3 py-1.5">
                                                {{ $talk->length }} min
                                            </span>
                                            <span class="rounded-full bg-gray-100 px-3 py-1.5">
                                                {{ $talk->type->label() }}
                                            </span>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>

                @can('viewSubmissions', $conference)
                    <div
                        id="panel-submissions"
                        class="p-7 sm:p-9"
                        role="tabpanel"
                        aria-labelledby="tab-submissions"
                        data-tab-panel="submissions"
                        hidden
                    >
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                Management
                            </p>
                            <h2 class="mt-2 text-2xl font-bold text-gray-950">
                                Talk submissions
                            </h2>
                            <p class="mt-2 text-sm text-gray-500">
                                Review proposals and update their status.
                            </p>
                        </div>

                        @if($submissions->isEmpty())
                            <div
                                class="mt-7 rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center">
                                <p class="font-semibold text-gray-900">No submissions yet.</p>
                            </div>
                        @else
                            <div class="mt-7 space-y-4">
                                @foreach($submissions as $talk)
                                    @php
                                        $submissionBio = $submissionBios->get($talk->pivot->bio_id);
                                    @endphp
                                    <article class="rounded-2xl border border-gray-200 p-5 sm:p-6">
                                        <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                                            <div class="min-w-0">
                                                <a href="{{route('talks.show',$talk->id)}}"
                                                   class="cursor-pointer hover:underline">
                                                    <h3 class="text-lg font-bold text-gray-950 sm:text-xl">
                                                        {{ $talk->title }}
                                                    </h3>
                                                </a>
                                                <p class="mt-2 leading-7 text-gray-600">
                                                    {{ Str::limit($talk->abstract, 200) }}
                                                </p>
                                            </div>

                                            @if($submissionBio)
                                                <div class="mt-5 rounded-xl bg-gray-50 p-4">
                                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">
                                                        Submitted bio
                                                    </p>

                                                    <p class="mt-2 font-semibold text-gray-950">
                                                        {{ $submissionBio->nickname }}
                                                    </p>

                                                    <p class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600">
                                                        {{ $submissionBio->bio }}
                                                    </p>
                                                </div>
                                            @else
                                                <p class="mt-4 text-sm font-medium text-amber-700">
                                                    No bio is attached to this submission.
                                                </p>
                                            @endif

                                            <form
                                                method="POST"
                                                action="{{ route('conferences.talks.status', [$conference, $talk]) }}"
                                                class="shrink-0"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <label class="sr-only" for="status-{{ $talk->id }}">
                                                    Submission status for {{ $talk->title }}
                                                </label>
                                                <select
                                                    id="status-{{ $talk->id }}"
                                                    name="status"
                                                    onchange="this.form.submit()"
                                                    class="w-full rounded-xl border-gray-300 bg-white text-sm font-semibold text-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500 lg:w-auto"
                                                >
                                                    @foreach($talkSubmissionStatuses as $status)
                                                        <option
                                                            value="{{ $status->value }}"
                                                            @selected($talk->pivot->status === $status)
                                                        >
                                                            {{ $status->label() }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </form>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endcan
            </section>

            <div class="mt-8 grid gap-8 md:grid-cols-3">
                <section class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm sm:p-8 md:col-span-2">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                Call for Papers
                            </p>
                            @if(! $isOwner)
                                <h2 class="mt-2 text-2xl font-bold text-gray-950">
                                    Submit your proposal
                                </h2>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">
                                    Select one of your talks and submit it to this conference.
                                </p>
                            @endif
                        </div>

                        @if($cfpIsOpen)
                            <span
                                class="w-fit rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">
                                Open
                            </span>
                        @elseif(now()->startOfDay()->lt($conference->cfp_starts_at->copy()->startOfDay()))
                            <span class="w-fit rounded-full bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700">
                                Upcoming
                            </span>
                        @else
                            <span class="w-fit rounded-full bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-600">
                                Closed
                            </span>
                        @endif
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-gray-50 p-5">
                            <p class="text-sm text-gray-500">Opens</p>
                            <p class="mt-2 text-lg font-semibold text-gray-950">
                                {{ $conference->cfp_starts_at->format('F d, Y') }}
                            </p>
                        </div>

                        <div class="rounded-2xl bg-gray-50 p-5">
                            <p class="text-sm text-gray-500">Deadline</p>
                            <p class="mt-2 text-lg font-semibold text-gray-950">
                                {{ $conference->cfp_ends_at->format('F d, Y') }}
                            </p>
                        </div>
                    </div>

                    @auth
                        @if(!$isOwner)
                            <div class="mt-8 border-t border-gray-200 pt-8">
                                @if($cfpIsOpen)
                                    @if($availableTalks->isNotEmpty())
                                        <form
                                            method="POST"
                                            data-talk-submission-form
                                            class="rounded-2xl border border-gray-200 bg-gray-50 p-5 sm:p-6"
                                        >
                                            @csrf

                                            <div>
                                                <label
                                                    for="talk-submission-url"
                                                    class="block text-sm font-semibold text-gray-900"
                                                >
                                                    Choose your talk
                                                </label>

                                                <select
                                                    id="talk-submission-url"
                                                    data-talk-submission-url
                                                    required
                                                    class="mt-3 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                                >
                                                    <option value="">Select a talk</option>

                                                    @foreach($availableTalks as $talk)
                                                        <option
                                                            value="{{ route('conferences.talks.submit', [$conference, $talk]) }}"
                                                            @selected(old('talk_id') === $talk->id)
                                                        >
                                                            {{ $talk->title }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mt-5">
                                                @if($bios->isNotEmpty())
                                                    <label
                                                        for="bio-id"
                                                        class="block text-sm font-semibold text-gray-900"
                                                    >
                                                        Choose your bio
                                                    </label>

                                                    <select
                                                        id="bio-id"
                                                        name="bio_id"
                                                        class="mt-3 w-full rounded-xl border-gray-300 bg-white text-sm text-gray-900 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                                    >
                                                        <option value="">Select a bio</option>

                                                        @foreach($bios as $bio)
                                                            <option
                                                                value="{{ $bio->id }}"
                                                                @selected((string) old('bio_id') === (string) $bio->id)
                                                            >
                                                                {{ $bio->nickname }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                                        <p class="text-sm text-amber-800">
                                                            Adding a bio helps organizers learn more about you before
                                                            your talk submission.
                                                        </p>

                                                        <a
                                                            href="{{ route('bios.create') }}"
                                                            class="mt-3 inline-flex text-sm font-semibold text-amber-900 underline hover:no-underline"
                                                        >
                                                            Create a bio
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>


                                            <button
                                                type="submit"
                                                class="mt-5 w-full rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                            >
                                                Submit talk
                                            </button>

                                        </form>
                                    @else
                                        <div
                                            class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 px-6 py-8 text-center">
                                            <p class="font-semibold text-gray-900">No available talks to submit.</p>
                                            <p class="mt-1 text-sm text-gray-500">
                                                Create a talk first, or all your talks have already been submitted here.
                                            </p>

                                            @if(\Illuminate\Support\Facades\Route::has('talks.create'))
                                                <a
                                                    href="{{ route('talks.create') }}"
                                                    class="mt-4 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                                                >
                                                    Create a talk
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div class="rounded-2xl bg-gray-50 px-6 py-5 text-sm text-gray-600">
                                        Talk submissions are not currently open.
                                    </div>
                                @endif

                                @if($mySubmissions->isNotEmpty())
                                    <div class="mt-8">
                                        <h3 class="text-lg font-bold text-gray-950">Your submissions</h3>

                                        <div class="mt-4 space-y-3">
                                            @foreach($mySubmissions as $talk)
                                                <div
                                                    class="flex flex-col gap-3 rounded-2xl border border-gray-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                                                    <div class="min-w-0">
                                                        <p class="truncate font-semibold text-gray-950">{{ $talk->title }}</p>
                                                        <p class="mt-1 text-sm text-gray-500">
                                                            {{ $talk->length }} min · {{ $talk->type->label() }}
                                                        </p>
                                                    </div>

                                                    <span
                                                        class="w-fit rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700">
                                                        {{ $talk->pivot->status->label()}}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        @if($cfpIsOpen)
                            <div class="mt-8 border-t border-gray-200 pt-8">
                                <div class="rounded-2xl border border-gray-200 bg-gray-50 px-6 py-6">
                                    <p class="font-semibold text-gray-900">Sign in to submit a talk.</p>

                                    @if(\Illuminate\Support\Facades\Route::has('login'))
                                        <a
                                            href="{{ route('login') }}"
                                            class="mt-4 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                                        >
                                            Sign in
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endauth
                </section>

                <aside class="space-y-8">
                    @if($conference->url)
                        <div class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm sm:p-8">
                            <h3 class="text-xl font-bold text-gray-950">Official website</h3>
                            <p class="mt-3 text-sm leading-6 text-gray-600">
                                Find more information, schedule and registration details.
                            </p>

                            <a
                                href="{{ $conference->url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-6 flex items-center justify-center gap-2 rounded-xl bg-gray-950 px-5 py-3 font-semibold text-white transition hover:bg-gray-800"
                            >
                                Visit website
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    @endif

                    <div class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm sm:p-8">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Organizer
                        </p>
                        <p class="mt-3 text-xl font-bold text-gray-950">
                            {{ $conference->user->name }}
                        </p>
                    </div>
                </aside>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tabsRoot = document.querySelector('[data-conference-tabs]');

            if (!tabsRoot) {
                return;
            }

            const buttons = [...tabsRoot.querySelectorAll('[data-tab-button]')];
            const panels = [...tabsRoot.querySelectorAll('[data-tab-panel]')];

            const activateTab = (tabName, updateHash = true) => {
                const activeButton = buttons.find(
                    button => button.dataset.tabButton === tabName
                );

                if (!activeButton) {
                    return;
                }

                buttons.forEach(button => {
                    const isActive = button === activeButton;

                    button.setAttribute('aria-selected', String(isActive));
                    button.setAttribute('tabindex', isActive ? '0' : '-1');
                    button.classList.toggle('border-gray-950', isActive);
                    button.classList.toggle('text-gray-950', isActive);
                    button.classList.toggle('border-transparent', !isActive);
                    button.classList.toggle('text-gray-500', !isActive);
                });

                panels.forEach(panel => {
                    panel.hidden = panel.dataset.tabPanel !== tabName;
                });

                if (updateHash) {
                    history.replaceState(null, '', `#${tabName}`);
                }
            };

            buttons.forEach((button, index) => {
                button.addEventListener('click', () => {
                    activateTab(button.dataset.tabButton);
                });

                button.addEventListener('keydown', event => {
                    let nextIndex = null;

                    if (event.key === 'ArrowRight') {
                        nextIndex = (index + 1) % buttons.length;
                    }

                    if (event.key === 'ArrowLeft') {
                        nextIndex = (index - 1 + buttons.length) % buttons.length;
                    }

                    if (event.key === 'Home') {
                        nextIndex = 0;
                    }

                    if (event.key === 'End') {
                        nextIndex = buttons.length - 1;
                    }

                    if (nextIndex === null) {
                        return;
                    }

                    event.preventDefault();
                    buttons[nextIndex].focus();
                    activateTab(buttons[nextIndex].dataset.tabButton);
                });
            });

            const requestedTab = window.location.hash.replace('#', '');
            const initialTab = buttons.some(
                button => button.dataset.tabButton === requestedTab
            ) ? requestedTab : 'details';

            activateTab(initialTab, false);

            const submissionForm = document.querySelector('[data-talk-submission-form]');
            const submissionSelect = document.querySelector('[data-talk-submission-url]');

            if (submissionForm && submissionSelect) {
                submissionForm.addEventListener('submit', event => {
                    if (!submissionSelect.value) {
                        event.preventDefault();
                        submissionSelect.focus();
                        return;
                    }

                    submissionForm.action = submissionSelect.value;
                });
            }
        });
    </script>
@endsection
