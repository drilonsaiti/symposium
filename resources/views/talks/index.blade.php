@extends('layouts.app')

@section('title', 'Your talks')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:py-16">

            @if (session('success') || session('status'))
                <div
                    class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-800">
                    {{ session('success') ?? session('status') }}
                </div>
            @endif

            <header class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        Speaker content
                    </p>

                    <h1 class="mt-2 text-4xl font-bold tracking-tight text-gray-950">
                        Your talks
                    </h1>

                    <p class="mt-3 max-w-2xl leading-7 text-gray-600">
                        Manage reusable talk proposals and prepare them for conference submissions.
                    </p>
                </div>

                @can('create', App\Models\Talk::class)
                    <a
                        href="{{ route('talks.create') }}"
                        class="inline-flex w-full shrink-0 items-center justify-center rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 sm:w-auto"
                    >
                        Create talk
                    </a>
                @endcan
            </header>

            @if ($talks->isEmpty())
                <section
                    class="mt-10 rounded-3xl border border-dashed border-gray-300 bg-white px-6 py-16 text-center shadow-sm">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-700">
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

                    <h2 class="mt-5 text-xl font-bold text-gray-950">
                        No talks yet
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-gray-500">
                        Create your first reusable talk proposal and submit it to upcoming conferences.
                    </p>

                    @can('create', App\Models\Talk::class)
                        <a
                            href="{{ route('talks.create') }}"
                            class="mt-6 inline-flex rounded-xl bg-gray-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-gray-800"
                        >
                            Create your first talk
                        </a>
                    @endcan
                </section>
            @else
                <div class="mt-10 flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-500">
                        {{ $talks->total() }}
                        {{ Str::plural('talk', $talks->total()) }}
                    </p>
                </div>

                <div class="mt-5 space-y-5">
                    @foreach ($talks as $talk)

                        <article
                            class="overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm transition hover:border-gray-300 hover:shadow-md">

                            <div class="p-6 sm:p-7">
                                <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <a
                                            href="{{ route('talks.show', $talk) }}"
                                            class="group inline-block"
                                        >
                                            <h2 class="text-xl font-bold text-gray-950 transition group-hover:underline group-hover:underline-offset-4 sm:text-2xl">
                                                {{ $talk->title }}
                                            </h2>
                                        </a>

                                        <div class="mt-4 flex flex-wrap items-center gap-2">
                                            <span
                                                class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700">
                                                {{ $talk->type->label() }}
                                            </span>

                                            <span
                                                class="rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-600">
                                                {{ $talk->length }} minutes
                                            </span>
                                        </div>
                                    </div>

                                    <p class="shrink-0 text-xs font-medium text-gray-400">
                                        Updated {{ $talk->updated_at->diffForHumans() }}
                                    </p>
                                </div>

                                <p class="mt-5 line-clamp-3 leading-7 text-gray-600">
                                    {{ Str::limit($talk->currentRevision->abstract ?? '', 200) }}
                                </p>

                            </div>

                            <div
                                class="flex flex-col gap-3 border-t border-gray-100 bg-gray-50/60 px-6 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-7">

                                <a
                                    href="{{ route('talks.show', $talk) }}"
                                    class="inline-flex items-center gap-2 text-sm font-semibold text-gray-900 transition hover:text-gray-600"
                                >
                                    View talk
                                    <span aria-hidden="true">→</span>
                                </a>

                                <div class="flex flex-wrap items-center gap-2">
                                    @can('update', $talk)
                                        <a
                                            href="{{ route('talks.edit', $talk) }}"
                                            class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                                        >
                                            Edit
                                        </a>
                                    @endcan

                                    @can('delete', $talk)
                                        <form
                                            action="{{ route('talks.destroy', $talk) }}"
                                            method="POST"
                                            onsubmit="return confirm('Delete this talk? This action cannot be undone.')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50"
                                            >
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>

                            </div>

                        </article>
                    @endforeach
                </div>

                @if ($talks->hasPages())
                    <div class="mt-10">
                        {{ $talks->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
@endsection
