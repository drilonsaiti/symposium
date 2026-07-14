@if ($errors->any())
    <div class="mb-7 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
        <h2 class="font-semibold text-red-800">
            Please fix the following errors:
        </h2>

        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-8">

    <section>
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                Conference information
            </p>

            <h2 class="mt-2 text-xl font-bold text-gray-950">
                General details
            </h2>

            <p class="mt-2 text-sm leading-6 text-gray-500">
                Add the public information speakers will see when reviewing the conference.
            </p>
        </div>

        <div class="mt-6 space-y-6">

            <div>
                <label
                    for="title"
                    class="block text-sm font-semibold text-gray-900"
                >
                    Conference title
                </label>

                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $conference->title ?? '') }}"
                    placeholder="For example: Laravel Live UK 2027"
                    required
                    autofocus
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                >

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Use the official public name of the conference.
                </p>
            </div>

            <div>
                <label
                    for="location"
                    class="block text-sm font-semibold text-gray-900"
                >
                    Location
                </label>

                <input
                    id="location"
                    type="text"
                    name="location"
                    value="{{ old('location', $conference->location ?? '') }}"
                    placeholder="For example: London, United Kingdom"
                    required
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                >

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Enter the city and country, venue, or specify that the event is online.
                </p>
            </div>

            <div>
                <div class="flex items-center justify-between gap-4">
                    <label
                        for="description"
                        class="block text-sm font-semibold text-gray-900"
                    >
                        Description
                    </label>

                    <span
                        id="description-character-count"
                        class="text-sm font-medium text-gray-500"
                    >
                        0 characters
                    </span>
                </div>

                <textarea
                    id="description"
                    name="description"
                    rows="7"
                    required
                    placeholder="Describe the conference, its audience, and what speakers can expect."
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 leading-7 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                >{{ old('description', $conference->description ?? '') }}</textarea>

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Provide enough context for speakers to decide whether the event fits their talk.
                </p>
            </div>

            <div>
                <label
                    for="url"
                    class="block text-sm font-semibold text-gray-900"
                >
                    Website URL
                    <span class="font-normal text-gray-500">optional</span>
                </label>

                <input
                    id="url"
                    type="url"
                    name="url"
                    value="{{ old('url', $conference->url ?? '') }}"
                    placeholder="https://conference.example.com"
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                >

                <p class="mt-2 text-sm leading-6 text-gray-500">
                    Link to the official conference website, registration page, or event information.
                </p>
            </div>

        </div>
    </section>

    <section class="border-t border-gray-200 pt-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                Schedule
            </p>

            <h2 class="mt-2 text-xl font-bold text-gray-950">
                Conference dates
            </h2>

            <p class="mt-2 text-sm leading-6 text-gray-500">
                Define when the conference begins and ends.
            </p>
        </div>

        <div class="mt-6 grid gap-5 sm:grid-cols-2">

            <div class="rounded-2xl bg-gray-50 p-5">
                <label
                    for="starts_at"
                    class="block text-sm font-semibold text-gray-900"
                >
                    Start date
                </label>

                <input
                    id="starts_at"
                    type="date"
                    name="starts_at"
                    value="{{ old(
                        'starts_at',
                        isset($conference) && $conference->starts_at
                            ? $conference->starts_at->format('Y-m-d')
                            : ''
                    ) }}"
                    required
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                >
            </div>

            <div class="rounded-2xl bg-gray-50 p-5">
                <label
                    for="ends_at"
                    class="block text-sm font-semibold text-gray-900"
                >
                    End date
                </label>

                <input
                    id="ends_at"
                    type="date"
                    name="ends_at"
                    value="{{ old(
                        'ends_at',
                        isset($conference) && $conference->ends_at
                            ? $conference->ends_at->format('Y-m-d')
                            : ''
                    ) }}"
                    required
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                >
            </div>

        </div>
    </section>

    <section class="border-t border-gray-200 pt-8">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                Call for Papers
            </p>

            <h2 class="mt-2 text-xl font-bold text-gray-950">
                Submission period
            </h2>

            <p class="mt-2 text-sm leading-6 text-gray-500">
                Define when speakers can submit talk proposals.
            </p>
        </div>

        <div class="mt-6 grid gap-5 sm:grid-cols-2">

            <div class="rounded-2xl bg-gray-50 p-5">
                <label
                    for="cfp_starts_at"
                    class="block text-sm font-semibold text-gray-900"
                >
                    CFP opens
                </label>

                <input
                    id="cfp_starts_at"
                    type="date"
                    name="cfp_starts_at"
                    value="{{ old(
                        'cfp_starts_at',
                        isset($conference) && $conference->cfp_starts_at
                            ? $conference->cfp_starts_at->format('Y-m-d')
                            : ''
                    ) }}"
                    required
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                >
            </div>

            <div class="rounded-2xl bg-gray-50 p-5">
                <label
                    for="cfp_ends_at"
                    class="block text-sm font-semibold text-gray-900"
                >
                    CFP deadline
                </label>

                <input
                    id="cfp_ends_at"
                    type="date"
                    name="cfp_ends_at"
                    value="{{ old(
                        'cfp_ends_at',
                        isset($conference) && $conference->cfp_ends_at
                            ? $conference->cfp_ends_at->format('Y-m-d')
                            : ''
                    ) }}"
                    required
                    class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm focus:border-gray-500 focus:ring-gray-500"
                >
            </div>

        </div>
    </section>

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-7 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ isset($conference) && $conference->exists
                ? route('conferences.show', $conference)
                : route('conferences.index') }}"
            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
        >
            Cancel
        </a>

        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
        >
            {{ $buttonText }}
        </button>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const description = document.getElementById('description');
        const characterCount = document.getElementById('description-character-count');

        const startsAt = document.getElementById('starts_at');
        const endsAt = document.getElementById('ends_at');
        const cfpStartsAt = document.getElementById('cfp_starts_at');
        const cfpEndsAt = document.getElementById('cfp_ends_at');

        const updateCharacterCount = () => {
            if (!description || !characterCount) {
                return;
            }

            const count = description.value.length;

            characterCount.textContent =
                `${count} ${count === 1 ? 'character' : 'characters'}`;
        };

        const updateDateConstraints = () => {
            if (startsAt && endsAt) {
                endsAt.min = startsAt.value;

                if (endsAt.value && startsAt.value && endsAt.value < startsAt.value) {
                    endsAt.value = startsAt.value;
                }
            }

            if (cfpStartsAt && cfpEndsAt) {
                cfpEndsAt.min = cfpStartsAt.value;

                if (
                    cfpEndsAt.value &&
                    cfpStartsAt.value &&
                    cfpEndsAt.value < cfpStartsAt.value
                ) {
                    cfpEndsAt.value = cfpStartsAt.value;
                }
            }

            if (cfpEndsAt && startsAt) {
                cfpEndsAt.max = startsAt.value;
            }
        };

        description?.addEventListener('input', updateCharacterCount);
        startsAt?.addEventListener('change', updateDateConstraints);
        cfpStartsAt?.addEventListener('change', updateDateConstraints);

        updateCharacterCount();
        updateDateConstraints();
    });
</script>
