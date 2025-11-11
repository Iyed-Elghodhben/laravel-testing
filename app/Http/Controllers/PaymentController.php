<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Http\Requests\StorePaymentRequest;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService)
    {
    }

    /**
     * POST /api/bookings/{id}/payment
     */
    public function pay(StorePaymentRequest $request, int $bookingId): JsonResponse
    {
        try {
            $payment = $this->paymentService->processPayment($bookingId, $request->validated());

            return response()->json([
                'message' => 'Payment successful',
                'payment' => $payment
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * GET /api/payments/{id}
     */
    public function show(int $paymentId): JsonResponse
    {
        $payment = $this->paymentService->getPayment($paymentId);

        if (!$payment) {
            return response()->json(['error' => 'Payment not found'], 404);
        }

        return response()->json($payment, 200);
    }
}
