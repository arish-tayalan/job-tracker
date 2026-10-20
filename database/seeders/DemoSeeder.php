<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\InterviewType;
use App\Models\User;
use Database\Factories\JobApplicationFactory;
use Illuminate\Database\Seeder;

/**
 * Demo account for the live site. Run it on its own:
 *
 *     php artisan db:seed --class=DemoSeeder
 *
 * It uses fixed data (no Faker, which isn't installed in production) and relative dates,
 * so the dashboard always looks current. Running it again resets the demo account only.
 */
class DemoSeeder extends Seeder
{
    public const EMAIL = 'demo@example.com';

    public const PASSWORD = 'DemoPass123';

    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => self::EMAIL],
            ['name' => 'Demo User', 'password' => self::PASSWORD, 'email_verified_at' => now()],
        );

        // Start clean. Interviews go too: the database cascades the delete.
        $user->jobApplications()->delete();

        $companies = JobApplicationFactory::companies();

        // [company index, position, status, applied N days ago (null = wishlist), follow-up in N days, interviews]
        // An interview is [days from today (negative = past), type, notes].
        $rows = [
            [0, 'Junior PHP Developer', ApplicationStatus::Offer, 35, null, [
                [-20, InterviewType::Phone, 'Quick chat with HR about the role and salary range.'],
                [-12, InterviewType::Technical, 'Built a small CRUD app live. Went well.'],
                [-5, InterviewType::Final, 'Met the CTO. Offer arrived two days later.'],
            ]],
            [1, 'Laravel Developer', ApplicationStatus::Interviewing, 21, 2, [
                [-6, InterviewType::Phone, 'Screening call. Asked about Eloquent relationships.'],
                [3, InterviewType::Technical, 'Pair programming session. Revise queues and testing.'],
            ]],
            [2, 'Junior Web Developer', ApplicationStatus::Interviewing, 14, null, [
                [-2, InterviewType::Hr, 'Culture fit chat, friendly team.'],
                [5, InterviewType::Technical, 'Take-home review with two engineers.'],
            ]],
            [3, 'Full Stack Developer', ApplicationStatus::Applied, 9, -1, []],
            [4, 'Graduate Software Engineer', ApplicationStatus::Applied, 6, 4, []],
            [5, 'Junior Laravel Developer', ApplicationStatus::Applied, 3, 7, []],
            [6, 'Web Developer', ApplicationStatus::Rejected, 28, null, [
                [-18, InterviewType::Phone, 'Screening call. They went with someone with more experience.'],
            ]],
            [7, 'Junior PHP Developer', ApplicationStatus::Rejected, 24, null, []],
            [8, 'Backend Developer (Junior)', ApplicationStatus::Withdrawn, 17, null, []],
            [11, 'Junior Web Developer', ApplicationStatus::Applied, 12, -3, []],
            [9, 'Junior Full Stack Developer', ApplicationStatus::Wishlist, null, null, []],
            [10, 'Laravel Developer', ApplicationStatus::Wishlist, null, null, []],
        ];

        foreach ($rows as [$index, $position, $status, $appliedDaysAgo, $followUpIn, $interviews]) {
            [$company, $location] = $companies[$index];

            $application = $user->jobApplications()->create([
                'company' => $company,
                'position' => $position,
                'status' => $status,
                'location' => $location,
                'applied_on' => $appliedDaysAgo === null ? null : today()->subDays($appliedDaysAgo),
                'follow_up_on' => $followUpIn === null ? null : today()->addDays($followUpIn),
                'notes' => null,
            ]);

            foreach ($interviews as [$daysFromToday, $type, $notes]) {
                $application->interviews()->create([
                    'scheduled_at' => today()->addDays($daysFromToday)->setTime(10, 30),
                    'type' => $type,
                    'notes' => $notes,
                ]);
            }
        }
    }
}
