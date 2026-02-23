<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Orbital Admin',
            'email' => 'admin@saturn.ph',
            'is_active' => true,
            'is_admin' => true,
        ]);

        User::factory()->create([
            'name' => 'Regular Teacher',
            'email' => 'teacher@saturn.ph',
            'is_active' => true,
            'is_admin' => false,
        ]);
    }
}
