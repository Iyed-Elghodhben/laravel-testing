<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all bookings
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            // Create a payment for each booking
            Payment::factory()->create([
                'booking_id' => $booking->id,
                'amount' => $booking->ticket->price * $booking->quantity,
            ]);
        }
    }
}
