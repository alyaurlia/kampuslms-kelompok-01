<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * GET /api/v1/notifications?page=&per_page=
     *
     * Hanya notifikasi milik user yang sedang login yang dikembalikan.
     */
    public function index(Request $request): NotificationCollection
    {
        $notifications = $request->user()
            ->notifications()
            ->latest('created_at')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100));

        return new NotificationCollection($notifications);
    }

    /**
     * POST /api/v1/notifications/{id}/read
     *
     * Hanya notifikasi milik user yang sedang login yang dapat ditandai
     * sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, string $id): NotificationResource
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($id)
            ->firstOrFail();

        $notification->markAsRead();

        return new NotificationResource($notification->fresh());
    }
}
