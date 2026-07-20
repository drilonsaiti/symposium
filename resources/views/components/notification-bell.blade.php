@props([
    'unreadCount' => 0,
    'latestNotifications' => collect(),
])

<div class="group relative">
    <button
        type="button"
        class="relative flex h-10 w-10 items-center justify-center rounded-xl text-gray-600 transition hover:bg-gray-100 hover:text-gray-950 focus:bg-gray-100 focus:outline-none"
        aria-label="Notifications"
        aria-haspopup="true"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="h-5 w-5"
            aria-hidden="true"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9a6 6 0 00-12 0v.75a8.967 8.967 0 01-2.312 6.022 23.848 23.848 0 005.455 1.31m5.714 0a3 3 0 11-5.714 0"
            />
        </svg>

        @if ($unreadCount > 0)
            <span
                class="absolute right-0 top-0 flex min-h-4 min-w-4 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white"
            >
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div class="absolute right-0 top-full h-3 w-full"></div>

    <div
        class="invisible absolute right-0 top-full z-50 mt-3 w-96 translate-y-1 overflow-hidden rounded-2xl border border-gray-200 bg-white opacity-0 shadow-xl transition duration-150 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100"
    >
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <div>
                <h2 class="text-sm font-bold text-gray-950">
                    Notifications
                </h2>

                @if ($unreadCount > 0)
                    <p class="mt-0.5 text-xs text-gray-500">
                        {{ $unreadCount }}
                        {{ Str::plural('unread notification', $unreadCount) }}
                    </p>
                @endif
            </div>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    @method('PATCH')

                    <button
                        type="submit"
                        class="text-xs font-semibold text-gray-600 transition hover:text-gray-950"
                    >
                        Mark all read
                    </button>
                </form>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse ($latestNotifications as $notification)
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
                        'relative block border-b border-gray-100 px-4 py-3 transition last:border-b-0 hover:bg-gray-50',
                        'bg-gray-50/70' => $isUnread,
                    ])
                >
                    <div class="flex gap-3">
                        <span
                            @class([
                                'mt-1.5 h-2 w-2 shrink-0 rounded-full',
                                'bg-blue-600' => $isUnread,
                                'bg-transparent' => ! $isUnread,
                            ])
                        ></span>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-3">
                                <p
                                    @class([
                                        'truncate text-sm text-gray-950',
                                        'font-bold' => $isUnread,
                                        'font-semibold' => ! $isUnread,
                                    ])
                                >
                                    {{ $title }}
                                </p>

                                <time
                                    datetime="{{ $notification->created_at->toIso8601String() }}"
                                    class="shrink-0 text-xs text-gray-400"
                                >
                                    {{ $notification->created_at->diffForHumans(short: true) }}
                                </time>
                            </div>

                            @if ($message)
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-gray-500">
                                    {{ $message }}
                                </p>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-10 text-center">
                    <p class="text-sm font-semibold text-gray-700">
                        No notifications
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        New notifications will appear here.
                    </p>
                </div>
            @endforelse
        </div>

        <a
            href="{{ route('notifications.index') }}"
            class="block border-t border-gray-100 px-4 py-3 text-center text-sm font-semibold text-gray-700 transition hover:bg-gray-50 hover:text-gray-950"
        >
            View all notifications
        </a>
    </div>
</div>
