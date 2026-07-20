@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-950">
                    Notifications
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Updates and activity related to your account.
                </p>
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
                    >
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>

        @if (session('status'))
            <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            @forelse ($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $title = $notification->data['title'] ?? 'Notification';
                    $message = $notification->data['message']
                        ?? $notification->data['body']
                        ?? null;
                @endphp

                <a
                    href="{{ route('notifications.show', $notification) }}"
                    @class([
                        'relative block border-b border-gray-100 px-5 py-5 transition last:border-b-0 hover:bg-gray-50 sm:px-6',
                        'bg-gray-50/70' => $isUnread,
                    ])
                >
                    <div class="flex gap-4">
                        <span
                            @class([
                                'mt-2 h-2.5 w-2.5 shrink-0 rounded-full',
                                'bg-blue-600' => $isUnread,
                                'bg-gray-200' => ! $isUnread,
                            ])
                        ></span>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                <h2
                                    @class([
                                        'text-sm text-gray-950',
                                        'font-bold' => $isUnread,
                                        'font-semibold' => ! $isUnread,
                                    ])
                                >
                                    {{ $title }}
                                </h2>

                                <time
                                    datetime="{{ $notification->created_at->toIso8601String() }}"
                                    class="shrink-0 text-xs text-gray-400"
                                >
                                    {{ $notification->created_at->diffForHumans() }}
                                </time>
                            </div>

                            @if ($message)
                                <p class="mt-2 text-sm leading-6 text-gray-600">
                                    {{ $message }}
                                </p>
                            @endif

                            @if ($isUnread)
                                <p class="mt-3 text-xs font-semibold text-blue-700">
                                    Unread
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-6 py-16 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            class="h-6 w-6"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 00-12 0v.75a8.967 8.967 0 01-2.312 6.022 23.848 23.848 0 005.455 1.31m5.714 0a3 3 0 11-5.714 0"
                            />
                        </svg>
                    </div>

                    <h2 class="mt-4 text-sm font-bold text-gray-950">
                        No notifications yet
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        New notifications will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
@endsection
