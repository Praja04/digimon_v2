<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::with('productionBatch')
            ->where('user_id', auth()->user()->id)
            ->orderByRaw("FIELD(status, 'unread', 'read')")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('app.notifications.index', compact('notifications'));
    }

    public function unreadNotifications()
    {
        $userId = auth()->user()->id;

        if (!$userId) {
            return response()->json(['error' => 'User ID tidak ditemukan di session'], 401);
        }

        $notifications = Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAllAsRead()
    {
        $userId = auth()->user()->id;

        if (!$userId) {
            return response()->json(['error' => 'User ID tidak ditemukan di session'], 401);
        }

        Notification::where('user_id', $userId)
            ->where('status', 'unread')
            ->update(['status' => 'read']);

        return response()->json(['success' => true]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return response()->json(['error' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notification->status = 'read';
        $notification->save();

        $redirectUrl = $this->getRedirectUrl($notification);

        return response()->json([
            'message' => 'Notifikasi sudah dibaca',
            'redirect_url' => $redirectUrl
        ]);
    }

    private function getRedirectUrl($notification)
    {
        $process = $notification->process;

        $routeMap = [
            'Pelarutan 1' => $notification->redirect,
            'Pelarutan 2' => $notification->redirect,
            'Blending Awal' => $notification->redirect,
            'Monitoring Turun Blending' => $notification->redirect,
            'Monitoring Pasteurisasi' => $notification->redirect,
            'Monitoring Storage Kimia' => $notification->redirect,
            'Monitoring Daily Tank Kimia' => $notification->redirect,
            'Monitoring On Going - Kimia' => $notification->redirect,
            'Monitoring On Going Mikro' => $notification->redirect,

        ];

        return $routeMap[$process] ?? route('notifications.index');
    }
}
