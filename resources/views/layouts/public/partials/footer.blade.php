<footer class="border-t border-gray-200 bg-gray-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <a
                href="{{ url('/') }}"
                class="flex items-center gap-3"
            >
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-950 text-xs font-bold text-white">
                    S
                </span>

                <span class="font-bold text-gray-950">
                    {{ config('app.name', 'Symposium') }}
                </span>
            </a>

            <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm font-medium text-gray-500">
                <a
                    href="{{ url('/#features') }}"
                    class="transition hover:text-gray-950"
                >
                    Features
                </a>

                <a
                    href="{{ url('/#workflow') }}"
                    class="transition hover:text-gray-950"
                >
                    Workflow
                </a>

                <a
                    href="{{ url('/#organizers') }}"
                    class="transition hover:text-gray-950"
                >
                    Organizers
                </a>

                @if(\Illuminate\Support\Facades\Route::has('conferences.index'))
                    <a
                        href="{{ route('conferences.index') }}"
                        class="transition hover:text-gray-950"
                    >
                        Conferences
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-7 flex flex-col gap-2 border-t border-gray-200 pt-6 text-sm text-gray-500 sm:flex-row sm:items-center sm:justify-between">
            <p>
                &copy; {{ now()->year }} {{ config('app.name', 'Symposium') }}.
            </p>

            <p>
                Talks, bios and conference submissions in one place.
            </p>
        </div>
    </div>
</footer>
