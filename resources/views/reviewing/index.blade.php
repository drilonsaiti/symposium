@extends('layouts.app')

@section('title', 'Your reviewing')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:py-16">

            @if (session('success') || session('status'))
                <div
                    class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800"
                >
                    {{ session('success') ?? session('status') }}
                </div>
            @endif

            <header>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                    Reviewer access
                </p>

                <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-950">
                    Your reviewing
                </h1>

                <p class="mt-3 max-w-2xl leading-7 text-gray-600">
                    Manage reviewer invitations and conferences where you review submissions.
                </p>
            </header>


            {{-- Pending invitations --}}
            <section class="mt-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-950">
                            Pending invitations
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Invitations waiting for your response.
                        </p>
                    </div>

                    @if ($pendingInvitations->isNotEmpty())
                        <span
                            class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-800"
                        >
                            {{ $pendingInvitations->count() }}
                        </span>
                    @endif
                </div>

                @if ($pendingInvitations->isEmpty())
                    <div
                        class="mt-5 rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow-sm"
                    >
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700"
                        >
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
                                    d="M5 6h14v12H5zM5 7l7 5 7-5"
                                />
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-gray-950">
                            No pending invitations
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                            New reviewer invitations will appear here.
                        </p>
                    </div>
                @else
                    <div class="mt-5 space-y-5">
                        @foreach ($pendingInvitations as $conference)
                            <article
                                class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-300 hover:shadow-md"
                            >
                                <div class="p-6 sm:p-7">
                                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0 flex-1">
                                            <a
                                                href="{{ route('conferences.show', $conference) }}"
                                                class="group inline-block"
                                            >
                                                <h3
                                                    class="text-xl font-bold text-gray-950 transition group-hover:underline group-hover:underline-offset-4 sm:text-2xl"
                                                >
                                                    {{ $conference->title }}
                                                </h3>
                                            </a>

                                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                                <span
                                                    class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-800"
                                                >
                                                    Invitation pending
                                                </span>

                                                @if ($conference->starts_at)
                                                    <span
                                                        class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600"
                                                    >
                                                        {{ $conference->starts_at->format('M j, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50/60 px-6 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                                >
                                    <a
                                        href="{{ route('conferences.show', $conference) }}"
                                        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 transition hover:text-gray-600"
                                    >
                                        View conference
                                        <span aria-hidden="true">→</span>
                                    </a>

                                    <div class="flex flex-wrap items-center gap-2">
                                        <form
                                            action="{{ route('reviewing.accept', $conference) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl bg-gray-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-800"
                                            >
                                                Accept
                                            </button>
                                        </form>

                                        <form
                                            action="{{ route('reviewing.decline', $conference) }}"
                                            method="POST"
                                            onsubmit="return confirm('Decline this reviewer invitation?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50"
                                            >
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>


            {{-- Active reviewing --}}
            <section class="mt-14">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-950">
                            Conferences you're reviewing
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Conferences where you currently have reviewer access.
                        </p>
                    </div>

                    @if ($reviewingConferences->isNotEmpty())
                        <span
                            class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700"
                        >
                            {{ $reviewingConferences->count() }}
                        </span>
                    @endif
                </div>

                @if ($reviewingConferences->isEmpty())
                    <div
                        class="mt-5 rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-10 text-center shadow-sm"
                    >
                        <div
                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700"
                        >
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
                                    d="M8 5h8M8 9h8M8 13h5M5 3h14v18H5z"
                                />
                            </svg>
                        </div>

                        <h3 class="mt-5 text-lg font-bold text-gray-950">
                            No active reviewing
                        </h3>

                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                            Conferences you accept reviewer access for will appear here.
                        </p>
                    </div>
                @else
                    <div class="mt-5 space-y-5">
                        @foreach ($reviewingConferences as $conference)
                            <article
                                class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-300 hover:shadow-md"
                            >
                                <div class="p-6 sm:p-7">
                                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0 flex-1">
                                            <a
                                                href="{{ route('conferences.show', $conference) }}"
                                                class="group inline-block"
                                            >
                                                <h3
                                                    class="text-xl font-bold text-gray-950 transition group-hover:underline group-hover:underline-offset-4 sm:text-2xl"
                                                >
                                                    {{ $conference->title }}
                                                </h3>
                                            </a>

                                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                                <span
                                                    class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-800"
                                                >
                                                    Active reviewer
                                                </span>

                                                @if ($conference->starts_at)
                                                    <span
                                                        class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600"
                                                    >
                                                        {{ $conference->starts_at->format('M j, Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50/60 px-6 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7"
                                >
                                    <a
                                        href="{{ route('conferences.show', $conference) }}#submissions"
                                        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 transition hover:text-gray-600"
                                    >
                                        Review submissions
                                        <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>
    </div>
@endsection
