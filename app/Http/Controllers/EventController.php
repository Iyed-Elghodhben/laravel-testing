<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\EventService;
use App\Http\Requests\StoreEventRequest;


class EventController extends Controller
{

    private EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }
    public function Events(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 10);
        $events = $this->eventService->getEvents($request->all(), $perPage);

        return response()->json($events);
    }
    public function getEventById($id): JsonResponse
    {
        $event = $this->eventService->getEventById($id);

        if (!$event) {
            return response()->json(['error' => 'Event not found'], 404);
        }

        return response()->json($event);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = $this->eventService->createEvent(
            $request->validated(),
            $request->user()
        );

        return response()->json([
            'message' => 'Event created successfully',
            'event' => $event
        ], 201);
    }

    public function update(StoreEventRequest $request, Event $event): JsonResponse
{
    $user = $request->user();


    $event->update($request->validated());

    return response()->json([
        'message' => 'Event updated successfully',
        'event' => $event
    ]);
}

    public function destroy(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        $event->delete();

        return response()->json([
            'message' => 'Event deleted successfully'
        ]);
    }

}
