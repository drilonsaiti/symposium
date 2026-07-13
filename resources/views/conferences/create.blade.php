@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 bg-white shadow-md rounded-xl p-8">

        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Create Conference
        </h1>

        <form method="POST" action="{{ route('conferences.store') }}" class="space-y-6">
            @csrf

            @include('conferences._form', [
                'buttonText' => 'Create Conference'
            ])
        </form>

    </div>
@endsection
