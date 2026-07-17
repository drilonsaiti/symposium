@extends('layouts.app')

@section('content')
    @php
        $user = auth()->user();

        $talks = $talks ?? collect();
        $bios = $bios ?? collect();
        $submissions = $submissions ?? collect();
        $ownedConferences = $ownedConferences ?? collect();
        $upcomingConferences = $upcomingConferences ?? collect();
         $getSubmissionStatus = function ($talk) {
        $status = $talk->conferences->first()?->pivot?->status;

        return $status?->value ?? $status;
    };

    $pendingSubmissions = $submissions->filter(
        fn ($talk) => $getSubmissionStatus($talk) === 'pending'
    );

    $acceptedSubmissions = $submissions->filter(
        fn ($talk) => $getSubmissionStatus($talk) === 'accepted'
    );

    $rejectedSubmissions = $submissions->filter(
        fn ($talk) => $getSubmissionStatus($talk) === 'rejected'
    );

        $talksUrl = \Illuminate\Support\Facades\Route::has('talks.index')
            ? route('talks.index')
            : '#';

        $createTalkUrl = \Illuminate\Support\Facades\Route::has('talks.create')
            ? route('talks.create')
            : $talksUrl;

        $biosUrl = \Illuminate\Support\Facades\Route::has('bios.index')
            ? route('bios.index')
            : '#';

        $createBioUrl = \Illuminate\Support\Facades\Route::has('bios.create')
            ? route('bios.create')
            : $biosUrl;

        $conferencesUrl = \Illuminate\Support\Facades\Route::has('conferences.index')
            ? route('conferences.index')
            : '#';
        $conferencesSubmittedUrl = \Illuminate\Support\Facades\Route::has('conferences.index')
            ? route('conferences.index',['view' => 'submitted'])
            : '#';

        $createConferenceUrl = \Illuminate\Support\Facades\Route::has('conferences.create')
            ? route('conferences.create')
            : $conferencesUrl;
    @endphp

    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">

            <header class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        Workspace
                    </p>

                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-950 sm:text-4xl">
                        Welcome back, {{ $user->name }}
                    </h1>

                    <p class="mt-3 max-w-2xl text-base leading-7 text-gray-600">
                        Manage your talks, speaker bios, conference submissions and organizer workflow.
                    </p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    @if(\Illuminate\Support\Facades\Route::has('talks.create'))
                        <a
                            href="{{ $createTalkUrl }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="h-4 w-4"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 5v14M5 12h14"
                                />
                            </svg>

                            Create talk
                        </a>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has('conferences.index'))
                        <a
                            href="{{ $conferencesUrl }}"
                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-gray-400 hover:bg-gray-50 hover:text-gray-950"
                        >
                            Browse conferences
                        </a>
                    @endif
                </div>
            </header>

            <section
                class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                aria-label="Workspace overview"
            >
                <a
                    href="{{ $talksUrl }}"
                    class="group rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-gray-300 hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Your talks
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight text-gray-950">
                                {{ $talks->count() }}
                            </p>
                        </div>

                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-950 text-white">
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
                                    d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"
                                />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-5 flex items-center justify-between text-sm">
                        <span class="font-semibold text-gray-700">
                            Manage talks
                        </span>

                        <span class="text-gray-400 transition group-hover:translate-x-1 group-hover:text-gray-700">
                            →
                        </span>
                    </div>
                </a>

                <a
                    href="{{ $biosUrl }}"
                    class="group rounded-3xl border border-gray-200 bg-white p-6 shadow-sm transition hover:border-gray-300 hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Speaker bios
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight text-gray-950">
                                {{ $bios->count() }}
                            </p>
                        </div>

                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-100 text-gray-700">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-5 w-5"
                                aria-hidden="true"
                            >
                                <circle cx="12" cy="8" r="3"/>
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6"
                                />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-5 flex items-center justify-between text-sm">
                        <span class="font-semibold text-gray-700">
                            Manage bios
                        </span>

                        <span class="text-gray-400 transition group-hover:translate-x-1 group-hover:text-gray-700">
                            →
                        </span>
                    </div>
                </a>

                <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">
                                Submissions
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight text-gray-950">
                                {{ $submissions->count() }}
                            </p>
                        </div>

                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-100 text-gray-700">
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
                                    d="M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 9h6M9 13h6M9 17h4"
                                />
                            </svg>
                        </span>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2 text-xs font-semibold">
                        <span class="rounded-full bg-amber-50 px-3 py-1.5 text-amber-700">
                            {{ $pendingSubmissions->count() }} pending
                        </span>

                        <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-emerald-700">
                            {{ $acceptedSubmissions->count() }} accepted
                        </span>

                        <span class="rounded-full bg-red-50 px-3.5 py-1.5 text-red-700">
    {{ $rejectedSubmissions->count() }} rejected
</span>
                    </div>
                </div>

                <a
                    href="{{ $conferencesUrl }}"
                    class="group rounded-3xl border border-gray-200 bg-gray-950 p-6 text-white shadow-sm transition hover:bg-gray-800 hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-400">
                                Organized conferences
                            </p>

                            <p class="mt-3 text-4xl font-bold tracking-tight">
                                {{ $ownedConferences->count() }}
                            </p>
                        </div>

                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-white">
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
                        </span>
                    </div>

                    <div class="mt-5 flex items-center justify-between text-sm">
                        <span class="font-semibold text-gray-200">
                            Manage conferences
                        </span>

                        <span class="text-gray-500 transition group-hover:translate-x-1 group-hover:text-white">
                            →
                        </span>
                    </div>
                </a>
            </section>

            <div class="mt-8 grid gap-8 xl:grid-cols-[1.4fr_0.6fr]">

                <div class="space-y-8">

                    <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                        <div
                            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-6 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                    Submission activity
                                </p>

                                <h2 class="mt-2 text-2xl font-bold text-gray-950">
                                    Recent submissions
                                </h2>
                            </div>

                            @if(\Illuminate\Support\Facades\Route::has('conferences.index'))
                                <a
                                    href="{{ $conferencesSubmittedUrl }}"
                                    class="text-sm font-semibold text-gray-600 transition hover:text-gray-950"
                                >
                                    Browse conferences →
                                </a>
                            @endif
                        </div>

                        @if($submissions->isEmpty())
                            <div class="px-6 py-12 text-center sm:px-8">
                                <span
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-600">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-6 w-6"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M9 9h6M9 13h6"
                                        />
                                    </svg>
                                </span>

                                <h3 class="mt-4 font-bold text-gray-950">
                                    No submissions yet
                                </h3>

                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                                    Browse open conferences and submit one of your existing talks with the appropriate
                                    speaker bio.
                                </p>

                                @if(\Illuminate\Support\Facades\Route::has('conferences.index'))
                                    <a
                                        href="{{ $conferencesUrl }}"
                                        class="mt-5 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                                    >
                                        Explore conferences
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="divide-y divide-gray-100">
                                @foreach($submissions->take(5) as $talk)
                                    @php
                                        $conference = $talk->conferences->first();

                                        $status = $conference?->pivot?->status;

                                        $statusValue = $status?->value ?? $status ?? 'pending';

                                        $statusLabel = $status && method_exists($status, 'label')
                                            ? $status->label()
                                            : str($statusValue)->replace('_', ' ')->title();

                                        $conferenceTitle = $conference?->title ?? 'Conference submission';

                                        $conferenceUrl = $conference && Route::has('conferences.show')
                                            ? route('conferences.show', $conference)
                                            : null;

                                        $statusClasses = match ($statusValue) {
                                            'accepted' => 'bg-emerald-50 text-emerald-700',
                                            'rejected' => 'bg-red-50 text-red-700',
                                            default => 'bg-amber-50 text-amber-700',
                                        };
                                    @endphp

                                    <article class="px-6 py-5 sm:px-8">
                                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span
                                                        class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses }}">
                                                        {{ $statusLabel }}
                                                    </span>

                                                    <span class="text-xs font-medium text-gray-400">
                                                        {{ $talk->pivot?->created_at?->diffForHumans() }}
                                                    </span>
                                                </div>

                                                <h3 class="mt-3 truncate text-base font-bold text-gray-950">
                                                    {{ $talk->title }}
                                                </h3>

                                                <p class="mt-1 truncate text-sm text-gray-500">
                                                    {{ $conferenceTitle }}
                                                </p>
                                            </div>

                                            <div class="flex shrink-0 items-center gap-3">
                                                @if($talk->length)
                                                    <span class="hidden text-sm font-medium text-gray-500 sm:inline">
                                                        {{ $talk->length }} min
                                                    </span>
                                                @endif

                                                @if($conferenceUrl)
                                                    <a
                                                        href="{{ $conferenceUrl }}"
                                                        class="inline-flex rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                                                    >
                                                        View
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    <section class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                        <div
                            class="flex flex-col gap-4 border-b border-gray-200 px-6 py-6 sm:flex-row sm:items-center sm:justify-between sm:px-8">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                    Speaker content
                                </p>

                                <h2 class="mt-2 text-2xl font-bold text-gray-950">
                                    Your latest talks
                                </h2>
                            </div>

                            <a
                                href="{{ $talksUrl }}"
                                class="text-sm font-semibold text-gray-600 transition hover:text-gray-950"
                            >
                                View all talks →
                            </a>
                        </div>

                        @if($talks->isEmpty())
                            <div class="px-6 py-12 text-center sm:px-8">
                                <h3 class="font-bold text-gray-950">
                                    Build your talk library
                                </h3>

                                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                                    Create reusable talks once, then submit them to multiple conferences.
                                </p>

                                @if(\Illuminate\Support\Facades\Route::has('talks.create'))
                                    <a
                                        href="{{ $createTalkUrl }}"
                                        class="mt-5 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                                    >
                                        Create your first talk
                                    </a>
                                @endif
                            </div>
                        @else
                            <div class="grid gap-4 p-6 sm:grid-cols-2 sm:p-8">
                                @foreach($talks->take(4) as $talk)
                                    <article
                                        class="rounded-2xl border border-gray-200 p-5 transition hover:border-gray-300 hover:shadow-sm">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="min-w-0">
                                                <h3 class="line-clamp-2 font-bold leading-6 text-gray-950">
                                                    {{ $talk->title }}
                                                </h3>

                                                <div class="mt-3 flex flex-wrap gap-2">
                                                    @if($talk->type)
                                                        <span
                                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                                            {{ method_exists($talk->type, 'label') ? $talk->type->label() : $talk->type }}
                                                        </span>
                                                    @endif

                                                    @if($talk->length)
                                                        <span
                                                            class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                                            {{ $talk->length }} min
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <p class="mt-4 line-clamp-3 text-sm leading-6 text-gray-500">
                                            {{ $talk->abstract }}
                                        </p>

                                        @if(\Illuminate\Support\Facades\Route::has('talks.show'))
                                            <a
                                                href="{{ route('talks.show', $talk) }}"
                                                class="mt-5 inline-flex text-sm font-semibold text-gray-700 transition hover:text-gray-950"
                                            >
                                                View talk →
                                            </a>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </div>

                <aside class="space-y-8">

                    <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm sm:p-7">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                            Quick actions
                        </p>

                        <h2 class="mt-2 text-xl font-bold text-gray-950">
                            Create something
                        </h2>

                        <div class="mt-6 space-y-3">
                            @if(\Illuminate\Support\Facades\Route::has('talks.create'))
                                <a
                                    href="{{ $createTalkUrl }}"
                                    class="group flex items-center justify-between rounded-2xl border border-gray-200 p-4 transition hover:border-gray-300 hover:bg-gray-50"
                                >
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-950 text-white">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                class="h-4 w-4"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M12 5v14M5 12h14"
                                                />
                                            </svg>
                                        </span>

                                        <div>
                                            <p class="text-sm font-bold text-gray-950">
                                                New talk
                                            </p>

                                            <p class="mt-0.5 text-xs text-gray-500">
                                                Add a reusable proposal
                                            </p>
                                        </div>
                                    </div>

                                    <span class="text-gray-400 transition group-hover:translate-x-1">
                                        →
                                    </span>
                                </a>
                            @endif

                            @if(\Illuminate\Support\Facades\Route::has('bios.create'))
                                <a
                                    href="{{ $createBioUrl }}"
                                    class="group flex items-center justify-between rounded-2xl border border-gray-200 p-4 transition hover:border-gray-300 hover:bg-gray-50"
                                >
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-700">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.8"
                                                class="h-5 w-5"
                                                aria-hidden="true"
                                            >
                                                <circle cx="12" cy="8" r="3"/>
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6"
                                                />
                                            </svg>
                                        </span>

                                        <div>
                                            <p class="text-sm font-bold text-gray-950">
                                                New bio
                                            </p>

                                            <p class="mt-0.5 text-xs text-gray-500">
                                                Create another bio version
                                            </p>
                                        </div>
                                    </div>

                                    <span class="text-gray-400 transition group-hover:translate-x-1">
                                        →
                                    </span>
                                </a>
                            @endif

                            @if(\Illuminate\Support\Facades\Route::has('conferences.create'))
                                <a
                                    href="{{ $createConferenceUrl }}"
                                    class="group flex items-center justify-between rounded-2xl border border-gray-200 p-4 transition hover:border-gray-300 hover:bg-gray-50"
                                >
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gray-100 text-gray-700">
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
                                        </span>

                                        <div>
                                            <p class="text-sm font-bold text-gray-950">
                                                New conference
                                            </p>

                                            <p class="mt-0.5 text-xs text-gray-500">
                                                Open your own CFP
                                            </p>
                                        </div>
                                    </div>

                                    <span class="text-gray-400 transition group-hover:translate-x-1">
                                        →
                                    </span>
                                </a>
                            @endif
                        </div>
                    </section>

                    <section class="rounded-3xl border border-gray-200 bg-gray-950 p-6 text-white shadow-sm sm:p-7">
                        @php
                            $profileSteps = [
                                'talk' => $talks->isNotEmpty(),
                                'bio' => $bios->isNotEmpty(),
                                'submission' => $submissions->isNotEmpty(),
                            ];

                            $completedSteps = collect($profileSteps)->filter()->count();
                            $completionPercentage = (int) round(($completedSteps / count($profileSteps)) * 100);
                        @endphp

                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                    Workspace setup
                                </p>

                                <h2 class="mt-2 text-xl font-bold">
                                    Profile readiness
                                </h2>
                            </div>

                            <span class="rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-gray-200">
                                {{ $completionPercentage }}%
                            </span>
                        </div>

                        <div class="mt-6 h-2 overflow-hidden rounded-full bg-white/10">
                            <div
                                class="h-full rounded-full bg-white transition-all"
                                style="width: {{ $completionPercentage }}%"
                            ></div>
                        </div>

                        <div class="mt-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $profileSteps['talk'] ? 'bg-emerald-400 text-gray-950' : 'bg-white/10 text-gray-400' }}">
                                    @if($profileSteps['talk'])
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                            class="h-3.5 w-3.5"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 12l4 4L19 6"
                                            />
                                        </svg>
                                    @else
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    @endif
                                </span>

                                <span
                                    class="text-sm font-medium {{ $profileSteps['talk'] ? 'text-white' : 'text-gray-400' }}">
                                    Create your first talk
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $profileSteps['bio'] ? 'bg-emerald-400 text-gray-950' : 'bg-white/10 text-gray-400' }}">
                                    @if($profileSteps['bio'])
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                            class="h-3.5 w-3.5"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 12l4 4L19 6"
                                            />
                                        </svg>
                                    @else
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    @endif
                                </span>

                                <span
                                    class="text-sm font-medium {{ $profileSteps['bio'] ? 'text-white' : 'text-gray-400' }}">
                                    Add a speaker bio
                                </span>
                            </div>

                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full {{ $profileSteps['submission'] ? 'bg-emerald-400 text-gray-950' : 'bg-white/10 text-gray-400' }}">
                                    @if($profileSteps['submission'])
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2.5"
                                            class="h-3.5 w-3.5"
                                            aria-hidden="true"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M5 12l4 4L19 6"
                                            />
                                        </svg>
                                    @else
                                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    @endif
                                </span>

                                <span
                                    class="text-sm font-medium {{ $profileSteps['submission'] ? 'text-white' : 'text-gray-400' }}">
                                    Submit to a conference
                                </span>
                            </div>
                        </div>
                    </section>

                    @if($upcomingConferences->isNotEmpty())
                        <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm sm:p-7">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                                        Discovery
                                    </p>

                                    <h2 class="mt-2 text-xl font-bold text-gray-950">
                                        Open conferences
                                    </h2>
                                </div>

                                <a
                                    href="{{ $conferencesUrl }}"
                                    class="text-sm font-semibold text-gray-500 transition hover:text-gray-950"
                                >
                                    All
                                </a>
                            </div>

                            <div class="mt-6 space-y-4">
                                @foreach($upcomingConferences->take(3) as $conference)
                                    <a
                                        href="{{ \Illuminate\Support\Facades\Route::has('conferences.show') ? route('conferences.show', $conference) : '#' }}"
                                        class="block rounded-2xl border border-gray-200 p-4 transition hover:border-gray-300 hover:bg-gray-50"
                                    >
                                        <p class="line-clamp-1 font-bold text-gray-950">
                                            {{ $conference->title }}
                                        </p>

                                        <div class="mt-2 flex items-center justify-between gap-4 text-xs">
                                            <span class="truncate font-medium text-gray-500">
                                                {{ $conference->location }}
                                            </span>

                                            @if($conference->cfp_ends_at)
                                                <span class="shrink-0 font-semibold text-gray-700">
                                                    Until {{ $conference->cfp_ends_at->format('M j') }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>
            </div>
        </div>
    </div>
@endsection
