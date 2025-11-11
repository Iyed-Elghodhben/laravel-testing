<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Http\Requests\StoreBookingRequest;
use App\Models\Booking;

class BookingController extends Controller
{
    private BookingService $bookingService;

    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }

    // Customer bookings
    public function index(Request $request)
    {
        $bookings = $this->bookingService->getCustomerBookings($request->user()->id);
        return response()->json($bookings);
    }

    // Book ticket
    public function store(StoreBookingRequest $request, $id)
    {
        $booking = $this->bookingService->createBooking(
            $id,
            $request->user()->id,
            $request->quantity
        );

        return response()->json([
            'message' => 'Booking confirmed!',
            'booking' => $booking
        ], 201);
    }

    // Cancel booking
    public function cancel($id, Booking $booking)
    {
        $booking = $this->bookingService->cancelBooking($id);

        return response()->json([
            'message' => 'Booking cancelled!',
            'booking' => $booking
        ]);
    }
}
