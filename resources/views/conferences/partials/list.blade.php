@php
    $today = now()->startOfDay();
@endphp

<div class="mb-6 flex items-center justify-between">
    <p class="text-sm font-medium text-gray-500">
        {{ $conferences->total() }}
        {{ Str::plural('conference', $conferences->total()) }}
    </p>
</div>
@if ($conferences->isEmpty())
    @if (request()->hasAny(['term', 'conference_date', 'cfp_status']))
        <section
            class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">

            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    class="h-6 w-6">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 21l-4.35-4.35m1.35-5.15a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                </svg>
            </div>

            <h2 class="mt-5 text-xl font-bold text-gray-950">
                No matching conferences found
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                Try changing your search or removing some filters.
            </p>

            <a
                href="{{ route('public.conferences.index') }}"
                class="mt-6 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800">
                Clear filters
            </a>

        </section>

    @else
        <section
            class="rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">

            <h2 class="mt-5 text-xl font-bold text-gray-950">
                No conferences available
            </h2>

            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                New conferences and Calls for Papers will appear here when they are published.
            </p>

            @can('create', App\Models\Conference::class)
                <a
                    href="{{ route('conferences.create') }}"
                    class="mt-6 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white">
                    Create the first conference
                </a>
            @endcan

        </section>
    @endif
@else
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($conferences as $conference)
            @php
                $cfpStartsAt = $conference->cfp_starts_at->copy()->startOfDay();
                $cfpEndsAt = $conference->cfp_ends_at->copy()->startOfDay();

                $cfpIsOpen = $today->betweenIncluded(
                    $cfpStartsAt,
                    $cfpEndsAt
                );

                $cfpIsUpcoming = $today->lt($cfpStartsAt);
            @endphp

            <article
                class="group relative flex flex-col overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition duration-200 hover:-translate-y-1 hover:border-gray-300 hover:shadow-lg">

                <a
                    href="{{ route('public.conferences.show', $conference) }}"
                    class="flex flex-1 w-full flex-col p-6 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-inset sm:p-7"
                    aria-label="View {{ $conference->title }}"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                Conference
                            </p>

                            <h2 class="mt-2 text-xl font-bold leading-snug text-gray-950 transition group-hover:text-gray-700 sm:text-2xl">
                                {{ $conference->title }}
                            </h2>
                        </div>

                        @if ($cfpIsOpen)
                            <span
                                class="shrink-0 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                            CFP open
                                        </span>
                        @elseif ($cfpIsUpcoming)
                            <span
                                class="shrink-0 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                                            Upcoming
                                        </span>
                        @else
                            <span
                                class="shrink-0 rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600">
                                            CFP closed
                                        </span>
                        @endif
                    </div>

                    <dl class="mt-6 space-y-4">
                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-600">
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
                                        d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1116 0z"
                                    />
                                    <circle cx="12" cy="10" r="2.5"/>
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <dt class="text-xs font-medium text-gray-500">
                                    Location
                                </dt>

                                <dd class="mt-1 break-words text-sm font-semibold text-gray-900">
                                    {{ $conference->location }}
                                </dd>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div
                                class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-600">
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
                                        d="M8 2v3m8-3v3M3.5 9h17M5 4.5h14a1.5 1.5 0 011.5 1.5v14A1.5 1.5 0 0119 21.5H5A1.5 1.5 0 013.5 20V6A1.5 1.5 0 015 4.5z"
                                    />
                                </svg>
                            </div>

                            <div>
                                <dt class="text-xs font-medium text-gray-500">
                                    Conference dates
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-gray-900">
                                    {{ $conference->starts_at->format('M d, Y') }}

                                    @if (!$conference->starts_at->isSameDay($conference->ends_at))
                                        <span class="font-normal text-gray-400">
                                                        to
                                                    </span>

                                        {{ $conference->ends_at->format('M d, Y') }}
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </dl>

                    <p class="mt-6 line-clamp-3 flex-1 text-sm leading-7 text-gray-600">
                        {{ $conference->description }}
                    </p>

                </a>

                <div class="relative z-10 mt-auto border-t border-gray-200 px-6 py-5 sm:px-7">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-gray-500">
                                Call for Papers
                            </p>

                            <p class="mt-2 text-sm font-semibold text-gray-900">
                                {{ $conference->cfp_starts_at->format('M d, Y') }}

                                <span class="font-normal text-gray-400">
                    to
                </span>

                                {{ $conference->cfp_ends_at->format('M d, Y') }}
                            </p>
                        </div>

                        <a
                            href="{{ route('public.conferences.show', $conference) }}"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gray-100 text-gray-600 transition group-hover:translate-x-1 group-hover:bg-gray-950 group-hover:text-white"
                            aria-label="View {{ $conference->title }}"
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
                                    d="M5 12h14m-6-6 6 6-6 6"
                                />
                            </svg>
                        </a>
                    </div>


                    @auth
                        @if(auth()->id() !== $conference->user_id)
                            @php
                                $currentStatus = $conference->is_favorited
                                    ? \App\Enum\ConferenceUserStatus::FAVORITED->value
                                    : (
                                        $conference->is_dismissed
                                            ? \App\Enum\ConferenceUserStatus::DISMISSED->value
                                            : ''
                                    );

                                $isFavorited = $currentStatus ===
                                    \App\Enum\ConferenceUserStatus::FAVORITED->value;

                                $isDismissed = $currentStatus ===
                                    \App\Enum\ConferenceUserStatus::DISMISSED->value;
                            @endphp

                            <div
                                class="mt-4 flex flex-wrap items-center gap-2"
                                data-conference-actions
                                data-status="{{ $currentStatus }}"
                            >
                                <button
                                    type="button"
                                    data-conference-action
                                    data-status-value="{{ \App\Enum\ConferenceUserStatus::FAVORITED->value }}"
                                    data-url="{{ route('conferences.favorite', $conference) }}"
                                    data-active-label="Saved"
                                    data-inactive-label="Save"
                                    data-active-classes="border-rose-200 bg-rose-50 text-rose-700"
                                    data-inactive-classes="border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50"
                                    aria-pressed="{{ $isFavorited ? 'true' : 'false' }}"
                                    class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-semibold transition disabled:cursor-wait disabled:opacity-50
                    {{ $isFavorited
                        ? 'border-rose-200 bg-rose-50 text-rose-700'
                        : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="{{ $isFavorited ? 'currentColor' : 'none' }}"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-4 w-4"
                                        data-active-fill
                                        aria-hidden="true"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 20.25S4.5 16 4.5 9.75A4.25 4.25 0 0112 7a4.25 4.25 0 017.5 2.75C19.5 16 12 20.25 12 20.25z"
                                        />
                                    </svg>

                                    <span data-action-label>
                    {{ $isFavorited ? 'Saved' : 'Save' }}
                </span>
                                </button>

                                <button
                                    type="button"
                                    data-conference-action
                                    data-status-value="{{ \App\Enum\ConferenceUserStatus::DISMISSED->value }}"
                                    data-url="{{ route('conferences.dismissed', $conference) }}"
                                    data-active-label="Dismissed"
                                    data-inactive-label="Dismiss"
                                    data-active-classes="border-gray-950 bg-gray-950 text-white"
                                    data-inactive-classes="border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50"
                                    aria-pressed="{{ $isDismissed ? 'true' : 'false' }}"
                                    class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-semibold transition disabled:cursor-wait disabled:opacity-50
                    {{ $isDismissed
                        ? 'border-gray-950 bg-gray-950 text-white'
                        : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300 hover:bg-gray-50' }}"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        class="h-4 w-4"
                                        aria-hidden="true"
                                    >
                                        <circle cx="12" cy="12" r="9"/>
                                        <path
                                            stroke-linecap="round"
                                            d="M9 9l6 6m0-6l-6 6"
                                        />
                                    </svg>

                                    <span data-action-label>
                    {{ $isDismissed ? 'Dismissed' : 'Dismiss' }}
                </span>
                                </button>
                            </div>
                        @endif

                    @endauth

                </div>

            </article>
        @endforeach
    </div>
@endif
@if ($conferences->hasPages())
    <div class="mt-12">
        {{ $conferences->links() }}
    </div>
@endif
