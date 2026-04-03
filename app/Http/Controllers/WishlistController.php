<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\SavedEvent;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function toggle(Event $event)
    {
        $user = auth()->user();

        $saved = SavedEvent::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($saved) {
            $saved->delete();
            $isSaved = false;
        } else {
            SavedEvent::create(['user_id' => $user->id, 'event_id' => $event->id]);
            $isSaved = true;
        }

        if (request()->expectsJson()) {
            return response()->json(['saved' => $isSaved]);
        }

        return back()->with('success', $isSaved ? 'Event saved to wishlist.' : 'Event removed from wishlist.');
    }
}
