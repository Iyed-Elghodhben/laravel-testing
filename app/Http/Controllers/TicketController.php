<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    /**
     * Inject TicketService via constructor
     */
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    /**
     * Create a new ticket for an event
     */
    public function store(StoreTicketRequest $request, int $eventId): JsonResponse
    {
        $ticket = $this->ticketService->createTicket($eventId, $request->validated());

        return response()->json([
            'message' => 'Ticket created successfully',
            'data' => $ticket
        ], 201);
    }

    /**
     * Update a ticket
     */
    public function update(UpdateTicketRequest $request, int $ticketId): JsonResponse
    {
        $ticket = $this->ticketService->updateTicket($ticketId, $request->validated());

        return response()->json([
            'message' => 'Ticket updated successfully',
            'data' => $ticket
        ]);
    }

    /**
     * Delete a ticket
     */
    public function destroy(int $ticketId): JsonResponse
    {
        try {
            $this->ticketService->deleteTicket($ticketId);

            return response()->json([
                'message' => 'Ticket deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Cannot delete ticket',
                'error' => $e->getMessage()
            ], 400);
        }
    }



}
