<x-guest-layout>
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <a
                href="{{ url('/') }}"
                class="inline-flex items-center gap-3"
            >
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-950 text-sm font-bold text-white shadow-sm">
                    S
                </span>


                <span class="text-xl font-bold tracking-tight text-gray-950">
                {{ config('app.name', 'Symposium') }}
            </span>
            </a>

            <h1 class="mt-7 text-3xl font-bold tracking-tight text-gray-950">
                Reset your password
            </h1>

            <p class="mt-3 text-sm leading-6 text-gray-500">
                Enter your email address and we will send you a secure password reset link.
            </p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl shadow-gray-950/5 sm:p-8">
            <x-auth-session-status
                class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                :status="session('status')"
            />

            <form
                method="POST"
                action="{{ route('password.email') }}"
                class="space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="email"
                        class="block text-sm font-semibold text-gray-900"
                    >
                        {{ __('Email address') }}
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                    >

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-2"
                    />
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-950 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    {{ __('Send reset link') }}
                </button>
            </form>

            @if(Route::has('login'))
                <div class="mt-6 border-t border-gray-200 pt-6 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="inline-flex items-center gap-2 text-sm font-semibold text-gray-700 transition hover:text-gray-950"
                    >
                        <span aria-hidden="true">←</span>
                        {{ __('Back to sign in') }}
                    </a>
                </div>
            @endif
        </div>

        <p class="mt-6 text-center text-xs leading-5 text-gray-400">
            The reset link will only be sent if an account exists for the provided email address.
        </p>
    </div>


</x-guest-layout>
