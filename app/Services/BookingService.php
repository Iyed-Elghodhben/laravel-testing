<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Ticket;
use App\Notifications\BookingConfirmed;

class BookingService
{
    // Create booking and notify user
    public function createBooking(int $ticketId, int $userId, int $quantity): Booking
    {
        $ticket = Ticket::findOrFail($ticketId);

        $booking = Booking::create([
            'user_id' => $userId,
            'ticket_id' => $ticket->id,
            'quantity' => $quantity,
            'status' => 'confirmed', // initially confirmed
        ]);



        // Send notification using queue
        $booking->user->notify((new BookingConfirmed($booking))->delay(now()->addSeconds(5)));

        return $booking;
    }

    // Get all bookings for a customer
    public function getCustomerBookings(int $userId)
    {
        return Booking::with('ticket.event')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    // Cancel booking
    public function cancelBooking(int $id)
    {
        $booking = Booking::findorFail($id);
        $booking->status = 'cancelled';
        $booking->save();

        return $booking;
    }
}

