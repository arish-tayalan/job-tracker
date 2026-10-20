<?php

namespace Database\Seeders;

use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\User;
use Database\Factories\JobApplicationFactory;
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
        // One application per company, using the first 15 companies from the list.
        $companies = collect(array_slice(JobApplicationFactory::companies(), 0, 15))
            ->map(fn (array $row) => ['company' => $row[0], 'location' => $row[1]])
            ->all();

        JobApplication::factory(15)->for($user)->sequence(...$companies)->create()
            ->take(5)
            ->each(fn (JobApplication $application) => Interview::factory()->for($application)->create());
    }
}
