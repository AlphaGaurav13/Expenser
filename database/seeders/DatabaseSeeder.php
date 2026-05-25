<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
        ]);

        User::factory()->create([
            'name' => 'Bob Jones',
            'email' => 'bob@example.com',
        ]);

        User::factory()->create([
            'name' => 'Charlie Brown',
            'email' => 'charlie@example.com',
        ]);
    }
}
