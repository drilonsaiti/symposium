<x-guest-layout>
    <div class="w-full max-w-md">
        <div class="mb-8 text-center">
            <a
                href="{{ url('/') }}"
                class="inline-flex items-center gap-3"
            >
                <span
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gray-950 text-sm font-bold text-white shadow-sm">
                    S
                </span>


                <span class="text-xl font-bold tracking-tight text-gray-950">
                {{ config('app.name', 'Symposium') }}
            </span>
            </a>

            <h1 class="mt-7 text-3xl font-bold tracking-tight text-gray-950">
                Create your account
            </h1>

            <p class="mt-3 text-sm leading-6 text-gray-500">
                Start managing your talks, bios and conference submissions in one place.
            </p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl shadow-gray-950/5 sm:p-8">
            <form
                method="POST"
                action="{{ route('register') }}"
                class="space-y-5"
            >
                @csrf

                <div>
                    <label
                        for="name"
                        class="block text-sm font-semibold text-gray-900"
                    >
                        {{ __('Name') }}
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Your full name"
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                    >

                    <x-input-error
                        :messages="$errors->get('name')"
                        class="mt-2"
                    />
                </div>

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
                        autocomplete="username"
                        placeholder="you@example.com"
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                    >

                    <x-input-error
                        :messages="$errors->get('email')"
                        class="mt-2"
                    />
                </div>

                <div>
                    <label
                        for="password"
                        class="block text-sm font-semibold text-gray-900"
                    >
                        {{ __('Password') }}
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="new-password"
                        placeholder="Create a secure password"
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                    >

                    <p class="mt-2 text-xs leading-5 text-gray-500">
                        Use at least 8 characters with a mix of letters, numbers and symbols.
                    </p>

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2"
                    />
                </div>

                <div>
                    <label
                        for="password_confirmation"
                        class="block text-sm font-semibold text-gray-900"
                    >
                        {{ __('Confirm password') }}
                    </label>

                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
                        placeholder="Repeat your password"
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                    >

                    <x-input-error
                        :messages="$errors->get('password_confirmation')"
                        class="mt-2"
                    />
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-950 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    {{ __('Create account') }}
                </button>
            </form>

            @if(Route::has('login'))
                <div class="mt-6 border-t border-gray-200 pt-6 text-center">
                    <p class="text-sm text-gray-500">
                        {{ __('Already have an account?') }}

                        <a
                            href="{{ route('login') }}"
                            class="font-semibold text-gray-950 transition hover:text-gray-600"
                        >
                            {{ __('Sign in') }}
                        </a>
                    </p>
                </div>
            @endif
        </div>

        <p class="mt-6 text-center text-xs leading-5 text-gray-400">
            By creating an account, you agree to use the platform responsibly.
        </p>
    </div>


</x-guest-layout>
