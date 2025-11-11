<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register()
    {
        $userData = User::factory()->customer()->make()->toArray();
        $userData['password'] = 'password';
        $userData['password_confirmation'] = 'password';

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'user' => ['id', 'name', 'email'],
                    'token'
                ]);

        $this->assertDatabaseHas('users', ['email' => $userData['email']]);
    }


    /** @test */
    public function user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['user', 'token']);
    }

    /** @test */
    public function organizer_can_create_event()
    {
        $user = User::factory()->create(['role' => 'organizer']);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->postJson('/api/events', [
            'title' => 'Test Event',
            'description' => 'Description',
            'date' => now()->addDays(5),
            'location' => 'Tunis'
        ], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Event created successfully']);
        $this->assertDatabaseHas('events', ['title' => 'Test Event']);
    }

    /** @test */
    public function customer_can_book_ticket()
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $organizer = User::factory()->create(['role' => 'organizer']);

        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        $token = $customer->createToken('auth_token')->plainTextToken;

        $response = $this->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 2
        ], ['Authorization' => "Bearer $token"]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Booking confirmed!']);
        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id
        ]);
    }

    /** @test */
    public function customer_cannot_double_book_same_ticket()
    {
        // Create a customer and organizer
        $customer = User::factory()->customer()->create();
        $organizer = User::factory()->organizer()->create();

        // Create an event and ticket
        $event = Event::factory()->create(['created_by' => $organizer->id]);
        $ticket = Ticket::factory()->create(['event_id' => $event->id]);

        // Existing confirmed booking
        Booking::factory()->confirmed()->create([
            'user_id' => $customer->id,
            'ticket_id' => $ticket->id
        ]);

        // Attempt to double book
        $token = $customer->createToken('auth_token')->plainTextToken;

        $response = $this->postJson("/api/tickets/{$ticket->id}/bookings", [
            'quantity' => 1
        ], [
            'Authorization' => "Bearer $token"
        ]);

        $response->assertStatus(409)
                ->assertJson([
                    'success' => false,
                    'message' => 'You have already booked this ticket.'
                ]);
    }

    /** @test */
public function organizer_can_create_ticket()
{
    $organizer = User::factory()->create(['role' => 'organizer']);
    $event = Event::factory()->create(['created_by' => $organizer->id]);

    $token = $organizer->createToken('auth_token')->plainTextToken;

    $payload = [
        'type' => 'VIP',
        'price' => 120,
        'quantity' => 50,
    ];

    $response = $this->postJson(
        "/api/events/{$event->id}/tickets",
        $payload,
        ['Authorization' => "Bearer $token"]
    );

    $response->assertStatus(201)
             ->assertJson(['message' => 'Ticket created successfully']);

    $this->assertDatabaseHas('tickets', [
        'event_id' => $event->id,
        'type' => 'VIP',
        'price' => 120,
    ]);
}

/** @test */
public function organizer_can_update_ticket()
{
    $organizer = User::factory()->create(['role' => 'organizer']);
    $event = Event::factory()->create(['created_by' => $organizer->id]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    $token = $organizer->createToken('auth_token')->plainTextToken;

    $data = [
        'type' => 'Premium',
        'price' => 150,
        'quantity' => 200,
    ];

    $response = $this->putJson(
        "/api/tickets/{$ticket->id}",
        $data,
        ['Authorization' => "Bearer $token"]
    );

    $response->assertStatus(200)
             ->assertJson(['message' => 'Ticket updated successfully']);

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'type' => 'Premium',
        'price' => 150,
    ]);
}

/** @test */
public function organizer_can_delete_ticket()
{
    $organizer = User::factory()->create(['role' => 'organizer']);
    $event = Event::factory()->create(['created_by' => $organizer->id]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    $token = $organizer->createToken('auth_token')->plainTextToken;

    $response = $this->deleteJson(
        "/api/tickets/{$ticket->id}",
        [],
        ['Authorization' => "Bearer $token"]
    );

    $response->assertStatus(200)
             ->assertJson(['message' => 'Ticket deleted successfully']);

    $this->assertDatabaseMissing('tickets', [
        'id' => $ticket->id
    ]);
}
/** @test */
public function user_can_pay_a_booking()
{

    $user = User::factory()->create(['role' => 'customer']);
    $organizer = User::factory()->create(['role' => 'organizer']);
    $event = Event::factory()->create(['created_by' => $organizer->id]);
    $ticket = Ticket::factory()->create(['event_id' => $event->id]);

    $booking = Booking::factory()->create([
        'user_id' => $user->id,
        'ticket_id' => $ticket->id,
        'quantity' => 1,
        'status' => 'pending'
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    // Act: remove 'currency' and 'payment_method'
    $response = $this->postJson("/api/bookings/{$booking->id}/payment", [
        'booking_id' => $booking->id,
        'amount' => $ticket->price,
    ], [
        'Authorization' => "Bearer $token"
    ]);



    // Assert
    $response->assertStatus(200)
             ->assertJson([
                 'message' => 'Payment successful',
                 'payment' => [
                     'booking_id' => $booking->id,
                     'status' => 'success'
                 ]
             ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $booking->id,
        'status' => 'confirmed'
    ]);
    $this->assertDatabaseHas('payments', [
        'booking_id' => $booking->id,
        'status' => 'success'
    ]);
}


/** @test */
public function user_can_view_payment_details()
{
    $user = User::factory()->create();

    // Create booking and payment using factories (without 'currency' and 'payment_method')
    $booking = Booking::factory()->create(['user_id' => $user->id]);
    $payment = Payment::factory()->success()->create([
        'booking_id' => $booking->id,
        'amount' => 100,
    ]);

    $token = $user->createToken('auth_token')->plainTextToken;

    // Act
    $response = $this->getJson("/api/payments/{$payment->id}", [
        'Authorization' => "Bearer $token"
    ]);

    // Assert
    $response->assertStatus(200)
             ->assertJson([
                 'id' => $payment->id,
                 'booking_id' => $booking->id,
                 'status' => 'success'
             ]);
}


}
