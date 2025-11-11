<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketService
{
    /**
     * Create a new ticket for an event
     */
    public function createTicket(int $eventId, array $data): Ticket
    {
        Event::findOrFail($eventId);

        return DB::transaction(function () use ($eventId, $data) {
            return Ticket::create([
                'event_id' => $eventId,
                'type' => $data['type'],
                'price' => $data['price'],
                'quantity' => $data['quantity'],
            ]);
        });
    }

    /**
     * Update an existing ticket
     */
    public function updateTicket(int $ticketId, array $data): Ticket
    {
        $ticket = Ticket::findOrFail($ticketId);


            $ticket->update([
                'type' => $data['type'] ?? $ticket->type,
                'price' => $data['price'] ?? $ticket->price,
                'quantity' => $data['quantity'] ?? $ticket->quantity,
            ]);

        return $ticket;
    }

    /**
     * Delete a ticket
     */
    public function deleteTicket(int $ticketId): bool
    {
        $ticket = Ticket::findOrFail($ticketId);

        // Check if ticket has bookings
        if ($ticket->bookings()->exists()) {
            throw new \Exception('Cannot delete ticket with existing bookings');
        }

        return DB::transaction(function () use ($ticket) {
            return $ticket->delete();
        });
    }

    /**
     * Get ticket by ID
     */
    public function getTicketById(int $ticketId): ?Ticket
    {
        return Ticket::with(['event', 'bookings'])->find($ticketId);
    }

    /**
     * Get all tickets for an event
     */
    public function getEventTickets(int $eventId)
    {
        return Ticket::where('event_id', $eventId)
            ->withCount('bookings')
            ->get();
    }
}
