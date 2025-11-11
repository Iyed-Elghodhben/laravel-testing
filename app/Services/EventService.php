<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class EventService
{

     public function getEvents(array $filters, int $perPage = 10)
    {
        $cacheKey = 'events_list_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, 60*5, function () use ($filters, $perPage) {
            $query = Event::query();

            if (!empty($filters['search'])) {
                $query->where('title', 'like', '%' . $filters['search'] . '%');
            }

            if (!empty($filters['date'])) {
                $query->whereDate('date', $filters['date']);
            }

            if (!empty($filters['location'])) {
                $query->where('location', 'like', '%' . $filters['location'] . '%');
            }

            return $query->orderBy('id')->cursorPaginate($perPage);
        });
    }


    public function getEventById(int $id): ?Event
    {
        return Event::with('tickets')->find($id);
    }

     public function createEvent(array $data, User $user): Event
    {
        return Event::create([
        'title'       => $data['title'],
        'description' => $data['description'] ?? null,
        'date'        => $data['date'],
        'location'    => $data['location'],
        'created_by'  => $user->id,
    ]);
    }


}
