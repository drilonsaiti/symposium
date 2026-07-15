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
                Welcome back
            </h1>

            <p class="mt-3 text-sm leading-6 text-gray-500">
                Sign in to manage your talks, bios and conference submissions.
            </p>
        </div>

        <div class="rounded-3xl border border-gray-200 bg-white p-6 shadow-xl shadow-gray-950/5 sm:p-8">
            <x-auth-session-status
                class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
                :status="session('status')"
            />

            <form
                method="POST"
                action="{{ route('login') }}"
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
                    <div class="flex items-center justify-between gap-4">
                        <label
                            for="password"
                            class="block text-sm font-semibold text-gray-900"
                        >
                            {{ __('Password') }}
                        </label>

                        @if(Route::has('password.request'))
                            <a
                                href="{{ route('password.request') }}"
                                class="text-xs font-semibold text-gray-500 transition hover:text-gray-950"
                            >
                                {{ __('Forgot password?') }}
                            </a>
                        @endif
                    </div>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                        class="mt-2 block w-full rounded-xl border-gray-300 bg-white px-4 py-3 text-sm text-gray-950 shadow-sm placeholder:text-gray-400 focus:border-gray-500 focus:ring-gray-500"
                    >

                    <x-input-error
                        :messages="$errors->get('password')"
                        class="mt-2"
                    />
                </div>

                <div class="flex items-center justify-between gap-4">
                    <label
                        for="remember_me"
                        class="inline-flex cursor-pointer items-center gap-3"
                    >
                        <input
                            id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="rounded border-gray-300 text-gray-950 shadow-sm focus:ring-gray-500"
                        >

                        <span class="text-sm font-medium text-gray-600">
                        {{ __('Remember me') }}
                    </span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-gray-950 px-5 py-3.5 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                    {{ __('Sign in') }}
                </button>
            </form>

            @if(Route::has('register'))
                <div class="mt-6 border-t border-gray-200 pt-6 text-center">
                    <p class="text-sm text-gray-500">
                        {{ __('Do not have an account?') }}

                        <a
                            href="{{ route('register') }}"
                            class="font-semibold text-gray-950 transition hover:text-gray-600"
                        >
                            {{ __('Create one') }}
                        </a>
                    </p>
                </div>
            @endif
        </div>

        <p class="mt-6 text-center text-xs leading-5 text-gray-400">
            By signing in, you can continue managing your speaker workspace.
        </p>
    </div>

</x-guest-layout>
