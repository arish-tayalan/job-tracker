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
     * Real Malaysian software companies used for demo data: [company name, location].
     *
     * @return list<array{0: string, 1: string}>
     */
    public static function companies(): array
    {
        return [
            ['Xantec Solutions', 'Johor Bahru'],
            ['My Coding Studio', 'Skudai, Johor'],
            ['SegWitz Tech', 'Kluang, Johor'],
            ['NETTO Solutions', 'Johor'],
            ['FITB Digital Agency', 'Johor Bahru'],
            ['Mountain Digital Agency', 'Johor Bahru'],
            ['Titanfour Business Solutions', 'Malaysia'],
            ['Upstack Studio', 'Malaysia'],
            ['AntDragon', 'Malaysia'],
            ['1Lynx Solutions', 'Malaysia'],
            ['ETCTECH Global', 'Kuala Lumpur'],
            ['Hezmedia Interactive', 'Petaling Jaya'],
            ['Hidden Chest Studios', 'Petaling Jaya'],
            ['MC Crenergy', 'Puchong'],
            ['Keyway Technologies', 'Malaysia'],
            ['Plentisoft', 'Shah Alam'],
            ['Access IT', 'Cyberjaya'],
            ['IO Web Studio', 'Puchong'],
            ['Webby Group', 'Puchong'],
            ['Softinn Solutions', 'Melaka'],
            ['O2O Digital', 'Petaling Jaya'],
            ['Inleads IT Solution', 'Kuala Lumpur'],
            ['VeecoTech', 'Penang'],
            ['Snappymob', 'Kuala Lumpur'],
            ['Agmo Holdings', 'Petaling Jaya'],
            ['Advisory Apps', 'Kuala Lumpur'],
            ['Exabytes', 'Penang'],
            ['AppPay', 'Puchong'],
            ['Gerbang Impian Holdings', 'Kajang'],
            ['Focus Software', 'Kuala Lumpur'],
        ];
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        [$company, $location] = fake()->randomElement(self::companies());

        return [
            'user_id' => User::factory(),
            'company' => $company,
            'position' => fake()->randomElement([
                'Junior PHP Developer',
                'Junior Web Developer',
                'Laravel Developer',
                'Graduate Software Engineer',
                'Full Stack Developer',
            ]),
            'status' => fake()->randomElement(ApplicationStatus::cases()),
            'location' => $location,
            'job_url' => fake()->optional()->url(),
            'applied_on' => fake()->dateTimeBetween('-2 months'),
            'follow_up_on' => fake()->optional(0.4)->dateTimeBetween('-1 week', '+2 weeks'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
