<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) ($request->input('per_page', 20));
        $perPage = max(1, min(50, $perPage));

        return response()->json(
            $request->user()->notifications()->latest()->paginate($perPage)
        );
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'unread' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markRead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array|min:1',
            'ids.*' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $request->user()
            ->unreadNotifications()
            ->whereIn('id', $request->ids)
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Notifications marquées comme lues']);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        return response()->json(['message' => 'Toutes les notifications sont lues']);
    }
}

