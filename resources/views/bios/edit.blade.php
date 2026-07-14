@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:py-16">

            <a
                href="{{ route('bios.show', $bio) }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 transition hover:text-gray-950"
            >
                <span aria-hidden="true">←</span>
                Back to bio
            </a>

            <section class="mt-6 overflow-hidden rounded-3xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 p-7 sm:p-9">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-gray-500">
                        Speaker profile
                    </p>

                    <h1 class="mt-2 text-3xl font-bold tracking-tight text-gray-950 sm:text-4xl">
                        Edit speaker bio
                    </h1>

                    <p class="mt-3 max-w-2xl leading-7 text-gray-600">
                        Update this bio version without affecting your other speaker bios.
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('bios.update', $bio) }}"
                    class="p-7 sm:p-9"
                >
                    @csrf
                    @method('PUT')

                    @include('bios._form', [
                        'buttonText' => 'Save changes',
                    ])
                </form>
            </section>

        </div>
    </div>
@endsection
