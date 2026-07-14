@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-10">

        <div class="bg-white shadow-md rounded-xl p-8">

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-bold text-gray-800">
                    {{ $talk->title }}
                </h1>




                <div class="space-x-2">
                    @can('update', $talk)
                    <a
                        href="{{ route('talks.edit', $talk) }}"
                        class="inline-block rounded-lg bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600"
                    >
                        Edit
                    </a>
                    @endcan

                        <a
                            href="{{ url()->previous() }}"
                            class="inline-block rounded-lg bg-gray-600 px-4 py-2 text-white hover:bg-gray-700"
                        >
                            Back
                        </a>
                </div>
            </div>

            <div class="space-y-6">

                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">
                        Title
                    </h2>
                    <p class="mt-1 text-gray-800">
                        {{ $talk->title }}
                    </p>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">
                        Length
                    </h2>
                    <p class="mt-1 text-gray-800">
                        {{ $talk->length }} minutes
                    </p>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">
                        Type
                    </h2>
                    <p class="mt-1 text-gray-800">
                        {{ $talk->type->label() }}
                    </p>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">
                        Abstract
                    </h2>
                    <p class="mt-1 text-gray-800 whitespace-pre-line">
                        {{ $talk->abstract }}
                    </p>
                </div>

                <div>
                    <h2 class="text-sm font-semibold text-gray-500 uppercase">
                        Organizer Notes
                    </h2>
                    <p class="mt-1 text-gray-800 whitespace-pre-line">
                        {{ $talk->organizer_notes ?: 'No organizer notes.' }}
                    </p>
                </div>

                <div class="border-t pt-6 text-sm text-gray-500">
                    <p>Created: {{ $talk->created_at->format('M d, Y H:i') }}</p>
                    <p>Last Updated: {{ $talk->updated_at->format('M d, Y H:i') }}</p>
                </div>

            </div>

        </div>

    </div>
@endsection
