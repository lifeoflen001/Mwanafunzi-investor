<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class CustomerNotificationController extends Controller
{
    public function index(Request $request)
    {
        return view('account.notifications', [
            'notifications' => $request->user()->notifications()->latest()->paginate(15),
            'unreadCount' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function read(Request $request, DatabaseNotification $notification)
    {
        $this->owner($request, $notification);
        $notification->markAsRead();

        return redirect()->to($notification->data['url'] ?? route('account.notifications'));
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All account updates marked as read.');
    }

    private function owner(Request $request, DatabaseNotification $notification): void
    {
        abort_unless(
            $notification->notifiable_type === $request->user()->getMorphClass()
                && (int) $notification->notifiable_id === (int) $request->user()->id,
            404,
        );
    }
}
