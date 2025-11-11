<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Booking;

class PreventDoubleBooking
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $ticketId = $request->route('id');

        // Check if the user already booked this ticket
        $existingBooking = Booking::where('user_id', $user->id)
                                  ->where('ticket_id', $ticketId)
                                  ->where('status', '!=', 'cancelled')
                                  ->first();

        if ($existingBooking) {
            return response()->json([
                'success' => false,
                'message' => 'You have already booked this ticket.'
            ], 409);
        }

        return $next($request);
    }
}
