@extends('layouts.public.app')

@section('title', 'Talks, bios and conference submissions')

@section('meta_description')
    Manage speaker bios, reusable talks, conference submissions, and review workflows in one place.
@endsection

@php
    $workspaceUrl = \Illuminate\Support\Facades\Route::has('dashboard')
    ? route('dashboard')
    : (
    \Illuminate\Support\Facades\Route::has('talks.index')
    ? route('talks.index')
    : url('/dashboard')
    );


    $conferenceUrl = \Illuminate\Support\Facades\Route::has('public.public.conferences.index')
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

@section('content') <section class="relative overflow-hidden bg-white"> <div
        class="pointer-events-none absolute inset-0 opacity-70"
        aria-hidden="true"
        style="
             background-image:
                 linear-gradient(to right, rgba(17, 24, 39, 0.035) 1px, transparent 1px),
                 linear-gradient(to bottom, rgba(17, 24, 39, 0.035) 1px, transparent 1px);
             background-size: 40px 40px;
             mask-image: linear-gradient(to bottom, black, transparent 85%);
         "
    ></div>


    <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-28">
        <div class="grid items-center gap-14 lg:grid-cols-[1.02fr_0.98fr] lg:gap-16">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3.5 py-2 text-xs font-semibold text-gray-700 shadow-sm">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    Built for speakers and conference teams
                </div>

                <h1 class="mt-7 text-5xl font-bold leading-[1.04] tracking-tight text-gray-950 sm:text-6xl lg:text-7xl">
                    Every talk, bio and submission.
                    <span class="text-gray-400">Finally connected.</span>
                </h1>

                <p class="mt-7 max-w-2xl text-lg leading-8 text-gray-600 sm:text-xl sm:leading-9">
                    Symposium gives speakers one place to manage reusable talks,
                    context-specific bios and conference submissions, while giving
                    organizers a clear workflow for reviewing proposals.
                </p>

                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    @auth
                        <a
                            href="{{ $workspaceUrl }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-950 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        >
                            Open your workspace

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
                                    d="M5 12h14m-6-6 6 6-6 6"
                                />
                            </svg>
                        </a>
                    @else
                        <a
                            href="{{ $guestPrimaryUrl }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-950 px-6 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                        >
                            Get started

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
                                    d="M5 12h14m-6-6 6 6-6 6"
                                />
                            </svg>
                        </a>
                    @endauth

                    <a
                        href="{{ $conferenceUrl }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3.5 text-sm font-semibold text-gray-700 transition hover:border-gray-400 hover:bg-gray-50 hover:text-gray-950"
                    >
                        Explore conferences
                    </a>
                </div>

                <div class="mt-10 grid max-w-2xl gap-4 sm:grid-cols-3">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-4 w-4"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"
                                />
                            </svg>
                        </span>

                        <span class="text-sm font-medium text-gray-600">
                            Reusable talks
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-4 w-4"
                                aria-hidden="true"
                            >
                                <circle cx="12" cy="8" r="3" />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6"
                                />
                            </svg>
                        </span>

                        <span class="text-sm font-medium text-gray-600">
                            Multiple bios
                        </span>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                class="h-4 w-4"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M4 6h16v12H4zM8 10h8M8 14h5"
                                />
                            </svg>
                        </span>

                        <span class="text-sm font-medium text-gray-600">
                            Submission tracking
                        </span>
                    </div>
                </div>
            </div>

            <div class="relative lg:pl-4">
                <div
                    class="absolute -inset-8 rounded-full bg-gray-200/60 blur-3xl"
                    aria-hidden="true"
                ></div>

                <div class="relative overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-2xl shadow-gray-950/10">
                    <div class="flex items-center justify-between border-b border-gray-200 bg-gray-50 px-5 py-4">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-gray-300"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-gray-300"></span>
                            <span class="h-2.5 w-2.5 rounded-full bg-gray-300"></span>
                        </div>

                        <div class="rounded-lg border border-gray-200 bg-white px-4 py-1.5 text-xs font-medium text-gray-500 shadow-sm">
                            symposium.app/workspace
                        </div>

                        <div class="w-14"></div>
                    </div>

                    <div class="grid min-h-[510px] grid-cols-[76px_1fr] sm:grid-cols-[180px_1fr]">
                        <aside class="border-r border-gray-200 bg-gray-50 p-3 sm:p-4">
                            <div class="mb-7 hidden items-center gap-2 px-2 sm:flex">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-950 text-xs font-bold text-white">
                                    S
                                </span>

                                <span class="text-sm font-bold text-gray-900">
                                    Workspace
                                </span>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-3 rounded-xl bg-gray-950 px-3 py-3 text-white">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-5 w-5 shrink-0"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"
                                        />
                                    </svg>

                                    <span class="hidden text-sm font-semibold sm:block">
                                        Talks
                                    </span>
                                </div>

                                <div class="flex items-center gap-3 rounded-xl px-3 py-3 text-gray-500">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-5 w-5 shrink-0"
                                        aria-hidden="true"
                                    >
                                        <circle cx="12" cy="8" r="3" />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6"
                                        />
                                    </svg>

                                    <span class="hidden text-sm font-semibold sm:block">
                                        Bios
                                    </span>
                                </div>

                                <div class="flex items-center gap-3 rounded-xl px-3 py-3 text-gray-500">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-5 w-5 shrink-0"
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M8 2v3m8-3v3M4 9h16M5 4.5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1v-14a1 1 0 011-1z"
                                        />
                                    </svg>

                                    <span class="hidden text-sm font-semibold sm:block">
                                        Conferences
                                    </span>
                                </div>
                            </div>
                        </aside>

                        <div class="min-w-0 p-4 sm:p-6">
                            <div class="flex items-end justify-between gap-4">
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-gray-400">
                                        Speaker content
                                    </p>

                                    <h2 class="mt-1 text-xl font-bold text-gray-950 sm:text-2xl">
                                        Your talks
                                    </h2>
                                </div>

                                <span class="hidden rounded-lg bg-gray-950 px-3 py-2 text-xs font-semibold text-white sm:inline-flex">
                                    Create talk
                                </span>
                            </div>

                            <div class="mt-6 space-y-4">
                                <article class="rounded-2xl border border-gray-200 p-4 shadow-sm sm:p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <h3 class="text-sm font-bold leading-snug text-gray-950 sm:text-base">
                                                Designing Laravel applications that last
                                            </h3>

                                            <div class="mt-3 flex flex-wrap gap-2">
                                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-600">
                                                    Conference talk
                                                </span>

                                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-[10px] font-semibold text-gray-600">
                                                    45 minutes
                                                </span>
                                            </div>
                                        </div>

                                        <span class="rounded-lg border border-gray-200 px-2.5 py-1.5 text-[10px] font-semibold text-gray-600">
                                            Edit
                                        </span>
                                    </div>

                                    <p class="mt-4 line-clamp-2 text-xs leading-5 text-gray-500">
                                        Practical architecture decisions for building maintainable
                                        applications without unnecessary complexity.
                                    </p>
                                </article>

                                <div class="grid gap-4 sm:grid-cols-2">
                                    <article class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-gray-400">
                                                Selected bio
                                            </p>

                                            <span class="text-[10px] font-semibold text-gray-400">
                                                52 words
                                            </span>
                                        </div>

                                        <p class="mt-3 text-sm font-bold text-gray-900">
                                            Laravel Live UK bio
                                        </p>

                                        <p class="mt-2 line-clamp-2 text-xs leading-5 text-gray-500">
                                            Short, focused version created specifically for the conference website.
                                        </p>
                                    </article>

                                    <article class="rounded-2xl border border-gray-200 bg-gray-950 p-4 text-white">
                                        <div class="flex items-center justify-between gap-3">
                                            <p class="text-[10px] font-semibold uppercase tracking-[0.14em] text-gray-400">
                                                Submission
                                            </p>

                                            <span class="rounded-full bg-amber-400/15 px-2 py-1 text-[10px] font-semibold text-amber-300">
                                                Under review
                                            </span>
                                        </div>

                                        <p class="mt-3 text-sm font-bold">
                                            Laravel Live UK
                                        </p>

                                        <p class="mt-2 text-xs leading-5 text-gray-400">
                                            Talk and selected bio submitted together.
                                        </p>
                                    </article>
                                </div>

                                <div class="rounded-2xl border border-gray-200 p-4">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-semibold text-gray-900">
                                            Submission progress
                                        </p>

                                        <p class="text-[10px] font-medium text-gray-400">
                                            Updated today
                                        </p>
                                    </div>

                                    <div class="mt-4 flex items-center">
                                        <span class="h-3 w-3 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
                                        <span class="h-0.5 flex-1 bg-emerald-200"></span>
                                        <span class="h-3 w-3 rounded-full bg-emerald-500 ring-4 ring-emerald-50"></span>
                                        <span class="h-0.5 flex-1 bg-gray-200"></span>
                                        <span class="h-3 w-3 rounded-full bg-gray-300 ring-4 ring-gray-100"></span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-3 text-[9px] font-semibold text-gray-400">
                                        <span>Submitted</span>
                                        <span class="text-center">Review</span>
                                        <span class="text-right">Decision</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="border-y border-gray-200 bg-gray-950 text-white">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="grid gap-6 md:grid-cols-[1fr_auto_1fr_auto_1fr] md:items-center">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                    Speakers
                </p>

                <p class="mt-2 text-base font-semibold">
                    Build a reusable library of talks and bios.
                </p>
            </div>

            <div class="hidden h-10 w-px bg-gray-800 md:block"></div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                    Submissions
                </p>

                <p class="mt-2 text-base font-semibold">
                    Choose the right version for every conference.
                </p>
            </div>

            <div class="hidden h-10 w-px bg-gray-800 md:block"></div>

            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                    Organizers
                </p>

                <p class="mt-2 text-base font-semibold">
                    Review complete, consistent proposals.
                </p>
            </div>
        </div>
    </div>
</section>

<section id="features" class="scroll-mt-24 bg-gray-50 py-20 sm:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                Real-world speaker management
            </p>

            <h2 class="mt-4 text-4xl font-bold tracking-tight text-gray-950 sm:text-5xl">
                Content changes by context.
                Your system should understand that.
            </h2>

            <p class="mt-6 text-lg leading-8 text-gray-600">
                A speaker does not have one universal talk description or one permanent
                bio. Symposium keeps each version organised and connects it to the
                conference where it was actually used.
            </p>
        </div>

        <div class="mt-12 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            <article class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-950 text-white">
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
                            d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"
                        />
                    </svg>
                </div>

                <h3 class="mt-6 text-xl font-bold text-gray-950">
                    Reusable talk library
                </h3>

                <p class="mt-3 leading-7 text-gray-600">
                    Store talk titles, formats, durations, abstracts and private
                    organizer notes once, then reuse them across submissions.
                </p>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                        class="h-6 w-6"
                        aria-hidden="true"
                    >
                        <circle cx="12" cy="8" r="3" />
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6"
                        />
                    </svg>
                </div>

                <h3 class="mt-6 text-xl font-bold text-gray-950">
                    Context-specific bios
                </h3>

                <p class="mt-3 leading-7 text-gray-600">
                    Keep short, long, formal, casual and conference-specific bios
                    without overwriting the version used somewhere else.
                </p>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm md:col-span-2 xl:col-span-1">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700">
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
                            d="M8 2v3m8-3v3M4 9h16M5 4.5h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1v-14a1 1 0 011-1z"
                        />
                    </svg>
                </div>

                <h3 class="mt-6 text-xl font-bold text-gray-950">
                    Conference discovery
                </h3>

                <p class="mt-3 leading-7 text-gray-600">
                    Explore conferences, review their Call for Papers window and
                    submit an existing talk without recreating your content.
                </p>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-white p-7 shadow-sm xl:col-span-2">
                <div class="grid gap-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700">
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
                                    d="M9 9h6M9 13h6M9 17h4"
                                />
                            </svg>
                        </div>

                        <h3 class="mt-6 text-xl font-bold text-gray-950">
                            Preserve submission history
                        </h3>

                        <p class="mt-3 leading-7 text-gray-600">
                            Know exactly which talk and which bio version were sent
                            to each conference, even after your main content changes.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    Laravel Live UK
                                </p>

                                <p class="mt-2 font-bold text-gray-950">
                                    Designing Laravel applications that last
                                </p>
                            </div>

                            <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                Accepted
                            </span>
                        </div>

                        <div class="mt-5 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl bg-white p-4 shadow-sm">
                                <p class="text-xs font-medium text-gray-500">
                                    Talk version
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    45-minute keynote
                                </p>
                            </div>

                            <div class="rounded-xl bg-white p-4 shadow-sm">
                                <p class="text-xs font-medium text-gray-500">
                                    Bio version
                                </p>

                                <p class="mt-1 text-sm font-semibold text-gray-900">
                                    UK conference bio
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>

            <article class="rounded-3xl border border-gray-200 bg-gray-950 p-7 text-white shadow-sm">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/10 text-white">
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
                            d="M4 12l5 5L20 6"
                        />
                    </svg>
                </div>

                <h3 class="mt-6 text-xl font-bold">
                    Clear submission status
                </h3>

                <p class="mt-3 leading-7 text-gray-400">
                    Track submitted, under review, accepted and rejected proposals
                    without searching through email threads.
                </p>
            </article>
        </div>
    </div>
</section>

<section id="workflow" class="scroll-mt-24 border-y border-gray-200 bg-white py-20 sm:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
            <div class="max-w-xl lg:sticky lg:top-28">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                    One connected workflow
                </p>

                <h2 class="mt-4 text-4xl font-bold tracking-tight text-gray-950 sm:text-5xl">
                    From first draft to conference decision.
                </h2>

                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Symposium removes repeated data entry while preserving the
                    differences that matter for each event.
                </p>

                <a
                    href="{{ $conferenceUrl }}"
                    class="mt-8 inline-flex items-center gap-2 text-sm font-semibold text-gray-950 transition hover:text-gray-600"
                >
                    Explore conferences
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <ol class="relative space-y-5">
                <div
                    class="absolute bottom-10 left-6 top-10 w-px bg-gray-200"
                    aria-hidden="true"
                ></div>

                <li class="relative rounded-3xl border border-gray-200 bg-gray-50 p-6 sm:p-7">
                    <div class="flex gap-5">
                        <span class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gray-950 text-sm font-bold text-white shadow-sm">
                            01
                        </span>

                        <div>
                            <h3 class="text-xl font-bold text-gray-950">
                                Create your talk once
                            </h3>

                            <p class="mt-2 leading-7 text-gray-600">
                                Add the title, session type, duration, abstract and
                                organizer-specific information.
                            </p>
                        </div>
                    </div>
                </li>

                <li class="relative rounded-3xl border border-gray-200 bg-gray-50 p-6 sm:p-7">
                    <div class="flex gap-5">
                        <span class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-sm font-bold text-gray-950 shadow-sm ring-1 ring-gray-200">
                            02
                        </span>

                        <div>
                            <h3 class="text-xl font-bold text-gray-950">
                                Choose the right bio
                            </h3>

                            <p class="mt-2 leading-7 text-gray-600">
                                Select the short, long, formal, casual or event-specific
                                bio that fits the conference requirements.
                            </p>
                        </div>
                    </div>
                </li>

                <li class="relative rounded-3xl border border-gray-200 bg-gray-50 p-6 sm:p-7">
                    <div class="flex gap-5">
                        <span class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-sm font-bold text-gray-950 shadow-sm ring-1 ring-gray-200">
                            03
                        </span>

                        <div>
                            <h3 class="text-xl font-bold text-gray-950">
                                Submit to a conference
                            </h3>

                            <p class="mt-2 leading-7 text-gray-600">
                                Connect the selected talk and bio to the conference
                                without duplicating your entire profile.
                            </p>
                        </div>
                    </div>
                </li>

                <li class="relative rounded-3xl border border-gray-200 bg-gray-50 p-6 sm:p-7">
                    <div class="flex gap-5">
                        <span class="relative z-10 flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-sm font-bold text-gray-950 shadow-sm ring-1 ring-gray-200">
                            04
                        </span>

                        <div>
                            <h3 class="text-xl font-bold text-gray-950">
                                Follow the decision
                            </h3>

                            <p class="mt-2 leading-7 text-gray-600">
                                Keep the proposal, attached bio and review status
                                together throughout the complete process.
                            </p>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>
</section>

<section id="organizers" class="scroll-mt-24 bg-gray-50 py-20 sm:py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-[2rem] border border-gray-200 bg-gray-950 shadow-xl">
            <div class="grid lg:grid-cols-[0.9fr_1.1fr]">
                <div class="p-8 text-white sm:p-12 lg:p-14">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        For conference organizers
                    </p>

                    <h2 class="mt-4 text-4xl font-bold tracking-tight sm:text-5xl">
                        Better submissions lead to better programmes.
                    </h2>

                    <p class="mt-6 text-lg leading-8 text-gray-400">
                        Review complete proposals, manage statuses and publish accepted
                        talks without chasing speakers for missing information.
                    </p>

                    <div class="mt-9 space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="h-3.5 w-3.5"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 12l4 4L19 6"
                                    />
                                </svg>
                            </span>

                            <p class="text-sm leading-6 text-gray-300">
                                View talks, speakers and bios in one submission record.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="h-3.5 w-3.5"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 12l4 4L19 6"
                                    />
                                </svg>
                            </span>

                            <p class="text-sm leading-6 text-gray-300">
                                Update submission status through a focused review workflow.
                            </p>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-white/10">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    class="h-3.5 w-3.5"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M5 12l4 4L19 6"
                                    />
                                </svg>
                            </span>

                            <p class="text-sm leading-6 text-gray-300">
                                Automatically present accepted talks in the public programme.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-800 bg-white p-5 sm:p-8 lg:border-l lg:border-t-0 lg:p-10">
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 shadow-sm">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                    Talk submission
                                </p>

                                <h3 class="mt-2 text-lg font-bold text-gray-950">
                                    Designing Laravel applications that last
                                </h3>

                                <p class="mt-2 text-sm text-gray-500">
                                    Submitted by Alex Morgan
                                </p>
                            </div>

                            <span class="w-fit rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                                Under review
                            </span>
                        </div>

                        <div class="mt-5 rounded-xl border border-gray-200 bg-white p-4">
                            <p class="text-xs font-semibold text-gray-500">
                                Abstract
                            </p>

                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                A practical session about architecture decisions,
                                boundaries and patterns for applications expected to evolve.
                            </p>
                        </div>

                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-xl border border-gray-200 bg-white p-4">
                                <p class="text-xs font-semibold text-gray-500">
                                    Session
                                </p>

                                <p class="mt-2 text-sm font-bold text-gray-900">
                                    Conference talk · 45 min
                                </p>
                            </div>

                            <div class="rounded-xl border border-gray-200 bg-white p-4">
                                <p class="text-xs font-semibold text-gray-500">
                                    Speaker bio
                                </p>

                                <p class="mt-2 text-sm font-bold text-gray-900">
                                    Formal · 82 words
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 flex flex-col gap-3 border-t border-gray-200 pt-5 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm font-medium text-gray-500">
                                Update submission decision
                            </p>

                            <select
                                class="rounded-xl border-gray-300 bg-white text-sm font-semibold text-gray-700 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                                aria-label="Example submission status"
                            >
                                <option>Under review</option>
                                <option>Accepted</option>
                                <option>Rejected</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="border-t border-gray-200 bg-white py-20 sm:py-24">
    <div class="mx-auto max-w-5xl px-4 text-center sm:px-6 lg:px-8">
        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-950 text-lg font-bold text-white shadow-sm">
            S
        </div>

        <h2 class="mt-7 text-4xl font-bold tracking-tight text-gray-950 sm:text-5xl">
            Your speaking work deserves a proper system.
        </h2>

        <p class="mx-auto mt-6 max-w-2xl text-lg leading-8 text-gray-600">
            Stop rewriting the same content, losing submission context and
            searching through email for conference decisions.
        </p>

        <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
            @auth
                <a
                    href="{{ $workspaceUrl }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Open your workspace
                    <span aria-hidden="true">→</span>
                </a>
            @else
                <a
                    href="{{ $guestPrimaryUrl }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gray-950 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                >
                    Create your account
                    <span aria-hidden="true">→</span>
                </a>
            @endauth

            <a
                href="{{ $conferenceUrl }}"
                class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
            >
                Browse conferences
            </a>
        </div>
    </div>
</section>


@endsection
