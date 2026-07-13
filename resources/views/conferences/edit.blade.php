@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 bg-white shadow-md rounded-xl p-8">

        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Edit Conference
        </h1>

        <form method="POST" action="{{ route('conferences.update', $conference) }}" class="space-y-6">
            @csrf
            @method('PUT')

            @include('conferences._form', [
                'buttonText' => 'Update Conference'
            ])
        </form>

    </div>
@endsection
