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
        $batchId = $notification->production_batch_id;
        $process = $notification->process;

        // Map process ke route yang sesuai
        $routeMap = [
            'GGA' => route('gga.show', $batchId),
            'GGAS' => route('ggas.show', $batchId),
            'Blending Awal' => route('analisa.blending-awal.show', $batchId),
            'Monitoring Turun Blending' => route('analisa.monitoring-turun-blending.show', $batchId),
            'Monitoring Pasteurisasi' => route('analisa.monitoring-pasteurisasi.show', $batchId),
        ];

        return $routeMap[$process] ?? route('notifications.index');
    }
}
