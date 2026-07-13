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
        value="{{ old('title', $conference->title ?? '') }}"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >
</div>

<!-- Location -->
<div>
    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
        Location
    </label>

    <input
        id="location"
        type="text"
        name="location"
        value="{{ old('location', $conference->location ?? '') }}"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >
</div>

<!-- Description -->
<div>
    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
        Description
    </label>

    <textarea
        id="description"
        name="description"
        rows="5"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >{{ old('description', $conference->description ?? '') }}</textarea>
</div>

<!-- URL -->
<div>
    <label for="url" class="block text-sm font-medium text-gray-700 mb-2">
        Website URL
    </label>

    <input
        id="url"
        type="url"
        name="url"
        value="{{ old('url', $conference->url ?? '') }}"
        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
    >
</div>

<!-- Conference Dates -->
<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">
            Starts At
        </label>

        <input
            id="starts_at"
            type="date"
            name="starts_at"
            value="{{ old('starts_at', $conference->starts_at ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
        >
    </div>

    <div>
        <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2">
            Ends At
        </label>

        <input
            id="ends_at"
            type="date"
            name="ends_at"
            value="{{ old('ends_at', $conference->ends_at ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
        >
    </div>
</div>

<!-- CFP Dates -->
<div class="grid grid-cols-2 gap-4">
    <div>
        <label for="cfp_starts_at" class="block text-sm font-medium text-gray-700 mb-2">
            CFP Starts At
        </label>

        <input
            id="cfp_starts_at"
            type="date"
            name="cfp_starts_at"
            value="{{ old('cfp_starts_at', $conference->cfp_starts_at ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
        >
    </div>

    <div>
        <label for="cfp_ends_at" class="block text-sm font-medium text-gray-700 mb-2">
            CFP Ends At
        </label>

        <input
            id="cfp_ends_at"
            type="date"
            name="cfp_ends_at"
            value="{{ old('cfp_ends_at', $conference->cfp_ends_at ?? '') }}"
            class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500"
        >
    </div>
</div>

<div class="flex justify-end">
    <button
        type="submit"
        class="rounded-lg bg-indigo-600 px-6 py-2 text-white font-medium hover:bg-indigo-700 transition"
    >
        {{ $buttonText }}
    </button>
</div>
