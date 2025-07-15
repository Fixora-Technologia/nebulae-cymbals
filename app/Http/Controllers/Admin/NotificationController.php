<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $notifications = DatabaseNotification::where('notifiable_type', get_class(Auth::user()))
            ->where('notifiable_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('admin.pages.notifications.index', compact('notifications'));
    }
    
    /**
     * Get unread notifications for the dropdown in the navbar.
     *
     * @return \Illuminate\Http\Response
     */
    public function getUnreadNotifications()
    {
        $user = Auth::user();
        $notifications = $user->unreadNotifications->take(5);
        $count = $user->unreadNotifications->count();
        
        return response()->json([
            'notifications' => $notifications,
            'count' => $count
        ]);
    }
    
    /**
     * Mark a notification as read.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        
        // Check if the notification belongs to the current user
        if ($notification->notifiable_id == Auth::id() && $notification->notifiable_type == get_class(Auth::user())) {
            $notification->markAsRead();
            return redirect()->back()->with('success', 'Notification marked as read.');
        }
        
        return redirect()->back()->with('error', 'Unauthorized access to notification.');
    }
    
    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
    
    /**
     * Delete a notification.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        
        // Check if the notification belongs to the current user
        if ($notification->notifiable_id == Auth::id() && $notification->notifiable_type == get_class(Auth::user())) {
            $notification->delete();
            return redirect()->back()->with('success', 'Notification deleted successfully.');
        }
        
        return redirect()->back()->with('error', 'Unauthorized access to notification.');
    }
}
