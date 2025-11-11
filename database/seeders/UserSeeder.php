<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Create 2 admins user
        User::factory(2)->admin()->create();

        // Create 3 organizer users
        User::factory(3)->organizer()->create();

        // Create 10 customer users
        User::factory(10)->customer()->create();
    }

}
