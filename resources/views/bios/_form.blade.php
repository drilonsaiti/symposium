@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-5 py-4">
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

<div class="space-y-7">

    <div>
        <label
            for="nickname"
            class="block text-sm font-semibold text-gray-900"
        >
            Bio nickname
        </label>

        <input
            id="nickname"
            type="text"
            name="nickname"
            value="{{ old('nickname', $bio->nickname ?? '') }}"
            placeholder="For example: Laravel Live UK short bio"
            required
            autofocus
            class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
        >

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Use a descriptive internal nickname so you can recognise this version later.
        </p>
    </div>

    <div>
        <div class="flex items-center justify-between gap-4">
            <label
                for="bio"
                class="block text-sm font-semibold text-gray-900"
            >
                Bio
            </label>

            <span
                id="bio-word-count"
                class="text-sm font-medium text-gray-500"
            >
                0 words
            </span>
        </div>

        <textarea
            id="bio"
            name="bio"
            rows="12"
            required
            placeholder="Write the speaker bio here..."
            class="mt-3 w-full rounded-xl border-gray-300 bg-white px-4 py-3 leading-7 text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
        >{{ old('bio', $bio->bio ?? '') }}</textarea>

        <p class="mt-2 text-sm leading-6 text-gray-500">
            Short conference bios are commonly around 50 words. Keynote introductions may require approximately 200 words.
        </p>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-7 sm:flex-row sm:items-center sm:justify-end">
        <a
            href="{{ route('bios.index') }}"
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

@push('scripts')
    <script>
        document.addEventListener('DOMbioLoaded', () => {
            const bio = document.getElementById('bio');
            const wordCount = document.getElementById('bio-word-count');

            if (!bio || !wordCount) {
                return;
            }

            const updateWordCount = () => {
                const value = bio.value.trim();
                const count = value ? value.split(/\s+/).length : 0;

                wordCount.textbio = `${count} ${count === 1 ? 'word' : 'words'}`;
            };

            bio.addEventListener('input', updateWordCount);

            updateWordCount();
        });
    </script>
@endpush
