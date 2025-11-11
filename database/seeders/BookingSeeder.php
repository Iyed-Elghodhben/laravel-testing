<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\Ticket;


class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $tickets = Ticket::all();

        foreach ($customers as $customer) {
            // Each customer makes 2 bookings
                Booking::factory(2)->create([
                    'user_id' => $customer->id,
                    'ticket_id' => $tickets->random()->id,
                ]);
        }
    }
}
