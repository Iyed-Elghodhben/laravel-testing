<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ExampleTest extends TestCase
{

    use RefreshDatabase;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();
    }

    /** @test */
    public function it_processes_a_payment_successfully()
    {
        $booking = Booking::factory()->create(['status' => 'pending']);

        $data = ['amount' => 100];

        $payment = $this->paymentService->processPayment($booking->id, $data);

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'amount' => 100,
            'status' => 'success',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);

        $this->assertInstanceOf(Payment::class, $payment);
    }

    /** @test */
    public function it_throws_exception_if_booking_already_paid()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Booking is already paid');

        $booking = Booking::factory()->create(['status' => 'confirmed']);

        $this->paymentService->processPayment($booking->id, ['amount' => 50]);
    }

    /** @test */
    public function it_can_get_a_payment_by_id()
    {
        $booking = Booking::factory()->create(['status' => 'pending']);
        $payment = $this->paymentService->processPayment($booking->id, ['amount' => 200]);

        $fetchedPayment = $this->paymentService->getPayment($payment->id);

        $this->assertNotNull($fetchedPayment);
        $this->assertEquals($payment->id, $fetchedPayment->id);
        $this->assertEquals($booking->id, $fetchedPayment->booking->id);
    }
}
