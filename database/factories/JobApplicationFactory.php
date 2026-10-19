<?php

namespace Database\Factories;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company' => fake()->company(),
            'position' => fake()->randomElement([
                'Junior PHP Developer',
                'Junior Web Developer',
                'Laravel Developer',
                'Graduate Software Engineer',
                'Full Stack Developer',
            ]),
            'status' => fake()->randomElement(ApplicationStatus::cases()),
            'location' => fake()->randomElement(['Remote', 'Kuala Lumpur', 'Petaling Jaya', 'Cyberjaya', 'Hybrid']),
            'job_url' => fake()->optional()->url(),
            'applied_on' => fake()->dateTimeBetween('-2 months'),
            'follow_up_on' => fake()->optional(0.4)->dateTimeBetween('-1 week', '+2 weeks'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
