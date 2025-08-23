<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        Notification::where('user_id', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }

    public function markRead($id)
    {
        $user = auth()->user();
        $notification = Notification::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $notification->read_at = now();
        $notification->save();
        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $user = auth()->user();
        $notification = Notification::where('user_id', $user->id)->where('id', $id)->firstOrFail();
        $notification->delete();
        return response()->json(['success' => true]);
    }
} 