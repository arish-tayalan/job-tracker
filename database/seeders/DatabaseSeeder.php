<?php

namespace Database\Seeders;

use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * Demo login: test@example.com / password
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Give the first few applications some interviews so the dashboard has data to show.
        JobApplication::factory(15)->for($user)->create()
            ->take(5)
            ->each(fn (JobApplication $application) => Interview::factory()->for($application)->create());
    }
}
