@if ($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4">
        <h2 class="font-semibold text-red-700 mb-2">
            Please fix the following errors:
        </h2>

        <ul class="list-disc list-inside text-red-600 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Title -->
<div>
    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
        Title
    </label>

    <input
        id="title"
        type="text"
        name="title"
        value="{{ old('title', $talk->title ?? '') }}"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >
</div>

<!-- Length -->
<div>
    <label for="length" class="block text-sm font-medium text-gray-700 mb-2">
        Length (minutes)
    </label>

    <input
        id="length"
        type="number"
        name="length"
        min="20"
        value="{{ old('length', $talk->length ?? '') }}"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >
</div>

<!-- Type -->
<div>
    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
        Type
    </label>

    <select
        id="type"
        name="type"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >
        <option value="">Select a type</option>

        @foreach($talkTypes as $talkType)
            <option
                value="{{ $talkType->value }}"
                {{ old('type', $talk->type ?? '') == $talkType->value ? 'selected' : '' }}
            >
                {{ $talkType->label() }}
            </option>
        @endforeach
    </select>
</div>

<!-- Abstract -->
<div>
    <label for="abstract" class="block text-sm font-medium text-gray-700 mb-2">
        Abstract
    </label>

    <textarea
        id="abstract"
        name="abstract"
        rows="5"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >{{ old('abstract', $talk->abstract ?? '') }}</textarea>
</div>

<!-- Organizer Notes -->
<div>
    <label for="organizer_notes" class="block text-sm font-medium text-gray-700 mb-2">
        Organizer Notes
    </label>

    <textarea
        id="organizer_notes"
        name="organizer_notes"
        rows="4"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >{{ old('organizer_notes', $talk->organizer_notes ?? '') }}</textarea>
</div>

<div class="flex justify-end">
    <button
        type="submit"
        class="rounded-lg bg-indigo-600 px-6 py-2 text-white font-medium hover:bg-indigo-700 transition"
    >
        {{ $buttonText }}
    </button>
</div>
