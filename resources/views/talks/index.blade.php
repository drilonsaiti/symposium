@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto mt-8 space-y-4">

        @foreach($talks as $idx => $talk)
            <div class="p-5 bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition">

                <div class="flex items-start justify-between">
                    <a href="{{route('talks.show',$talk)}}">
                        <h2 class="text-lg font-semibold text-gray-800">
                            {{ $idx + 1 }}. {{ $talk->title }}
                        </h2>
                    </a>

                    <span class="text-xs px-2 py-1 rounded-full bg-indigo-100 text-indigo-700">
                        {{ ucfirst($talk->type->label()) }}
                    </span>
                </div>

                <p class="mt-2 text-gray-600 text-sm leading-relaxed">
                    {{ $talk->abstract }}
                </p>

                @can('update', $talk)
                    <a
                        href="{{ route('talks.edit', $talk) }}"
                        class="rounded-lg bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600"
                    >
                        Edit
                    </a>
                @endcan

                @can('delete', $talk)
                    <form action="{{ route('talks.destroy', $talk) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')



                        <button
                            type="submit"
                            class="rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700"
                        >
                            Delete
                        </button>
                    </form>
                @endcan

            </div>
        @endforeach

    </div>
@endsection
