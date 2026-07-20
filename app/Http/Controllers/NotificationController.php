<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    //

    public function index(): View
    {
        $user = auth()->user();

        $notifications = $user
            ->notifications()
            ->latest()
            ->paginate(15);

        $unreadCount = $user
            ->unreadNotifications()
            ->count();

        return view('notifications.index', compact(
            'notifications',
            'unreadCount'
        ));
    }

    public function show(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless($notification->notifiable_id === $request->user()->id &&
        $notification->notifiable_type === $request->user()->getMorphClass(), 404);

        if($notification->unread()){
            $notification->markAsRead();
        }

        $url = $notification->data['url'] ?? $notification->data['action_url'] ?? route('notifications.index');

        return redirect()->to($url);
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }
}
