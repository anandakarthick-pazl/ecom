<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::currentTenant()
            ->forAdmin()
            ->latest()
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function getUnread()
    {
        // Get total unread count first
        $totalUnreadCount = Notification::currentTenant()
            ->forAdmin()
            ->unread()
            ->count();

        // Get limited notifications for display
        $notifications = Notification::currentTenant()
            ->forAdmin()
            ->unread()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $notification->data
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count' => $totalUnreadCount // Return total count, not just the limited results
        ]);
    }

    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::currentTenant()->forAdmin()->unread()->update([
            'is_read' => true,
            'read_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Notification $notification)
    {
        $notification->delete();

        return response()->json(['success' => true]);
    }

    public function checkNew(Request $request)
    {
        $lastCheckTime = $request->get('last_check');
        
        $query = Notification::currentTenant()->forAdmin()->unread();
        
        if ($lastCheckTime) {
            $query->where('created_at', '>', $lastCheckTime);
        }
        
        $newNotifications = $query->latest()->get();
        
        // Get total unread count for badge display
        $totalUnreadCount = Notification::currentTenant()
            ->forAdmin()
            ->unread()
            ->count();
        
        return response()->json([
            'hasNew' => $newNotifications->count() > 0,
            'count' => $totalUnreadCount, // Return total unread count for badge
            'notifications' => $newNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'icon' => $notification->icon,
                    'color' => $notification->color,
                    'created_at' => $notification->created_at->diffForHumans(),
                    'data' => $notification->data
                ];
            })
        ]);
    }
}
