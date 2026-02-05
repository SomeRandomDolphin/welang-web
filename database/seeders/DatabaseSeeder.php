<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::where('email', 'admin@welang.com')->exists()) {
            User::factory()->unverified()->create([
                'name' => 'Admin Welang',
                'email' => 'admin@welang.com',
                'password' => 'admin-welang123',
                'is_admin' => true
            ]);
        }

        Category::factory(5)->create();
    }
}
