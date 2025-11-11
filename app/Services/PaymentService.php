<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
class PaymentService
{
    /**
     * Process a payment (mock)
     */
public function processPayment(int $bookingId, array $data): Payment
{
    try {
        $booking = Booking::findOrFail($bookingId);

        if ($booking->status === 'confirmed') {
            throw new \Exception('Booking is already paid');
        }

        return DB::transaction(function () use ($booking, $data) {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'amount' => $data['amount'],
                'status' => 'success', // conforme à l'ENUM
            ]);

            $booking->update(['status' => 'confirmed']);

            return $payment;
        });
    } catch (\Exception $e) {
        throw new \Exception('Payment failed: ' . $e->getMessage());
    }
}



    /**
     * Get payment by ID
     */
    public function getPayment(int $paymentId): ?Payment
    {
        return Payment::find($paymentId);
    }
}
