<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Support\AdminAudit;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->validate(['filter' => ['nullable', 'in:all,unread']])['filter'] ?? 'all';
        $notifications = ContactMessage::with('businessUnit')
            ->when($filter === 'unread', fn ($query) => $query->whereNull('read_at'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.notifications.index', compact('notifications', 'filter'));
    }

    public function read(ContactMessage $message)
    {
        if (! $message->read_at) {
            $message->update(['read_at' => now()]);
            AdminAudit::record('notification.read', 'Marked enquiry notification as read for '.$message->email, $message);
        }

        return to_route('admin.messages.show', $message);
    }

    public function readAll()
    {
        $count = ContactMessage::whereNull('read_at')->update(['read_at' => now()]);
        if ($count) AdminAudit::record('notification.read_all', 'Marked '.$count.' enquiry notifications as read');

        return back()->with('success', $count ? 'All enquiry notifications marked as read.' : 'There are no unread notifications.');
    }
}
