<div
    id="search-modal"
    class="fixed inset-0 z-[100] hidden items-start justify-center overflow-y-auto bg-gray-950/60 px-4 py-8 backdrop-blur-sm sm:py-16"
    role="dialog"
    aria-modal="true"
    aria-labelledby="search-modal-title"
>
    <button
        type="button"
        class="absolute inset-0 cursor-default"
        aria-label="Close search"
        onclick="closeSearchModal()"
    ></button>

    <div class="relative z-10 w-full max-w-3xl overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="border-b border-gray-200 px-5 py-5 sm:px-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h1
                        id="search-modal-title"
                        class="text-lg font-bold text-gray-950"
                    >
                        Search
                    </h1>

                    <p id="search-modal-subtitle" class="mt-1 hidden text-sm text-gray-500"></p>
                </div>

                <button
                    type="button"
                    onclick="closeSearchModal()"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-gray-500 transition hover:bg-gray-100 hover:text-gray-950"
                >
                    <span class="sr-only">Close</span>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-5 w-5"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="mt-5">
                <label for="search-modal-input" class="sr-only">
                    Search talks and bios
                </label>

                <div class="relative">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                        aria-hidden="true"
                    >
                        <circle cx="11" cy="11" r="8"></circle>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"></path>
                    </svg>

                    <input
                        id="search-modal-input"
                        type="search"
                        placeholder="Search talks and bios..."
                        autocomplete="off"
                        class="h-12 w-full rounded-xl border border-gray-300 bg-white pl-12 pr-4 text-sm text-gray-950 outline-none transition placeholder:text-gray-400 focus:border-gray-950 focus:ring-2 focus:ring-gray-950/10"
                    >
                </div>
            </div>
        </div>

        <div id="search-modal-body" class="max-h-[65vh] overflow-y-auto px-5 py-5 sm:px-6">
            <div class="py-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-6 w-6"
                        aria-hidden="true"
                    >
                        <circle cx="11" cy="11" r="8"></circle>
                        <path stroke-linecap="round" d="m21 21-4.35-4.35"></path>
                    </svg>
                </div>

                <h2 class="mt-4 font-semibold text-gray-950">Start searching</h2>

                <p class="mt-1 text-sm text-gray-500">Search through your talks and speaker bios.</p>
            </div>
        </div>
    </div>
</div>
