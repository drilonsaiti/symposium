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

@php
    $selectedType = old(
        'type',
        data_get($talk ?? null, 'type.value', data_get($talk ?? null, 'type'))
    );
@endphp

<div class="space-y-7">

    <div>
        <label
            for="title"
            class="block text-sm font-semibold text-gray-900"
        >
            Title
        </label>

        <input
            id="title"
            type="text"
            name="title"
            value="{{ old('title', $talk->title ?? '') }}"
            placeholder="For example: Building Maintainable Laravel Applications"
            required
            autofocus
            class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
        >

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Use a clear and specific title that communicates what attendees will learn.
        </p>
    </div>

    <div class="grid gap-7 sm:grid-cols-2">

        <div>
            <label
                for="length"
                class="block text-sm font-semibold text-gray-900"
            >
                Length
            </label>

            <div class="relative mt-3">
                <input
                    id="length"
                    type="number"
                    name="length"
                    min="20"
                    value="{{ old('length', $talk->length ?? '') }}"
                    placeholder="45"
                    required
                    class="w-full rounded-xl border-gray-300 bg-white px-4 py-3 pr-20 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                >

                <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm font-medium text-gray-500">
                    minutes
                </span>
            </div>

            <p class="mt-2 text-sm leading-6 text-gray-500">
                Minimum length: 20 minutes.
            </p>
        </div>

        <div>
            <label
                for="type"
                class="block text-sm font-semibold text-gray-900"
            >
                Talk type
            </label>

            <select
                id="type"
                name="type"
                required
                class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm focus:border-gray-500 focus:ring-gray-500"
            >
                <option value="">Select a type</option>

                @foreach ($talkTypes as $talkType)
                    <option
                        value="{{ $talkType->value }}"
                        @selected((string) $selectedType === (string) $talkType->value)
                    >
                        {{ $talkType->label() }}
                    </option>
                @endforeach
            </select>

            <p class="mt-2 text-sm leading-6 text-gray-500">
                Select the format that best matches the proposed session.
            </p>
        </div>

    </div>

    <div>
        <div class="flex items-center justify-between gap-4">
            <label
                for="abstract"
                class="block text-sm font-semibold text-gray-900"
            >
                Abstract
            </label>

            <span
                id="abstract-word-count"
                class="text-sm font-medium text-gray-500"
            >
                0 words
            </span>
        </div>

        <textarea
            id="abstract"
            name="abstract"
            rows="8"
            required
            placeholder="Describe the problem, key topics, and what attendees will take away from the session."
            class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 leading-7 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
        >{{ old('abstract', $talk->abstract ?? '') }}</textarea>

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Focus on the value for attendees rather than only listing technologies.
        </p>
    </div>

    <div>
        <label
            for="organizer_notes"
            class="block text-sm font-semibold text-gray-900"
        >
            Organizer notes
            <span class="font-normal text-gray-500">optional</span>
        </label>

        <textarea
            id="organizer_notes"
            name="organizer_notes"
            rows="5"
            placeholder="Add equipment requirements, scheduling constraints, or other information for organizers."
            class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 leading-7 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
        >{{ old('organizer_notes', $talk->organizer_notes ?? '') }}</textarea>

        <p class="mt-2 text-sm leading-6 text-gray-500">
            These notes are intended for conference organizers and are not part of the public abstract.
        </p>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-7 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ isset($talk) && $talk->exists
                ? route('talks.show', $talk)
                : route('talks.index') }}"
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
        const abstract = document.getElementById('abstract');
        const wordCount = document.getElementById('abstract-word-count');

        if (!abstract || !wordCount) {
            return;
        }

        const updateWordCount = () => {
            const value = abstract.value.trim();
            const count = value ? value.split(/\s+/).length : 0;

            wordCount.textContent = `${count} ${count === 1 ? 'word' : 'words'}`;
        };

        abstract.addEventListener('input', updateWordCount);

        updateWordCount();
    });
</script>
