<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Get organizers
        $organizers = User::where('role', 'organizer')->get();

        $eventCount = 0;
        foreach ($organizers as $organizer) {
            // Create 1-2 events per organizer to total 5
            $eventsToCreate = ($eventCount < 5) ? min(2, 5 - $eventCount) : 0;
            
            if ($eventsToCreate > 0) {
                Event::factory($eventsToCreate)->create([
                    'created_by' => $organizer->id,
                ]);
                $eventCount += $eventsToCreate;
            }
        }  
    }
}
