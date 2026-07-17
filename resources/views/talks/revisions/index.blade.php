@extends('layouts.app')

@section('content')
    <div class="min-h-screen">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:py-16">


            <a href="{{ route('talks.edit', $talk) }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 transition hover:text-gray-950"
            >
                <span aria-hidden="true">←</span>
                Back to edit
            </a>

            @if (session('status'))
                <div
                    class="mt-6 rounded-2xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                <header class="border-b border-gray-200 p-7 sm:p-9">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        Speaker content
                    </p>
                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-950 sm:text-4xl">
                        Abstract revisions
                    </h1>
                    <p class="mt-3 max-w-2xl leading-7 text-gray-600">
                        Restoring an older abstract creates a new revision with that content — nothing is deleted.
                    </p>
                </header>

                <div class="divide-y divide-gray-200">
                    @forelse ($revisions as $revision)
                        <div class="flex items-start justify-between gap-4 p-7 sm:p-9">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $revision->created_at->format('M j, Y \a\t g:i A') }}
                                    </p>

                                    @if ($talk->currentRevision && $talk->currentRevision->id === $revision->id)
                                        <span
                                            class="rounded-full bg-gray-950 px-2.5 py-0.5 text-xs font-semibold text-white">
                            Current
                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-gray-600">
                                    {{ $revision->abstract }}
                                </p>
                            </div>

                            @if (! ($talk->currentRevision && $talk->currentRevision->id === $revision->id))
                                <form method="POST" action="{{ route('talks.revisions.restore', [$talk, $revision]) }}">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="inline-flex shrink-0 items-center justify-center rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                                    >
                                        Restore
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="p-7 text-sm text-gray-500 sm:p-9">
                            No revisions yet.
                        </p>
                    @endforelse
                </div>

                @if ($revisions->hasPages())
                    <div class="border-t border-gray-200 px-7 py-5 sm:px-9">
                        {{ $revisions->onEachSide(1)->links() }}
                    </div>
                @endif
            </section>

        </div>
    </div>
@endsection
