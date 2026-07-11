@extends('layouts.app')

@section('content')
    <div class="max-w-2xl mx-auto mt-10 bg-white shadow-md rounded-xl p-8">

        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            Create Talk
        </h1>

        <form method="POST" action="{{ route('talks.store') }}" class="space-y-6">
            @csrf

            @include('talks._form', [
                'buttonText' => 'Create Talk'
            ])
        </form>

    </div>
@endsection
