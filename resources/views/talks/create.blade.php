@extends('layouts.app')

@section('content')
    <div class="min-h-screen ">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:py-16">

            <a
                href="{{ route('talks.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 transition hover:text-gray-950"
            >
                <span aria-hidden="true">←</span>
                Back to talks
            </a>

            <section class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">

                <header class="border-b border-gray-200 p-7 sm:p-9">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500">
                        Speaker content
                    </p>

                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-950 sm:text-4xl">
                        Create a talk
                    </h1>

                    <p class="mt-3 max-w-2xl leading-7 text-gray-600">
                        Create a reusable talk proposal that can be submitted to multiple conferences.
                    </p>
                </header>

                <form
                    method="POST"
                    action="{{ route('talks.store') }}"
                    class="p-7 sm:p-9"
                >
                    @csrf

                    @include('talks._form', [
                        'buttonText' => 'Create talk',
                    ])
                </form>

            </section>

        </div>
    </div>
@endsection
