<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_empty_dashboard_shows_a_friendly_message(): void
    {
        $this->actingAs(User::factory()->create())->get('/dashboard')
            ->assertOk()
            ->assertSee('Nothing to show yet');
    }

    public function test_dashboard_shows_headline_numbers(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['status' => ApplicationStatus::Wishlist]);
        JobApplication::factory()->for($user)->create(['status' => ApplicationStatus::Applied]);
        JobApplication::factory()->for($user)->create(['status' => ApplicationStatus::Interviewing]);
        JobApplication::factory()->for($user)->create(['status' => ApplicationStatus::Offer]);
        JobApplication::factory()->for($user)->create(['status' => ApplicationStatus::Rejected]);

        $response = $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->assertSame(5, $response->viewData('total'));
        $this->assertSame(2, $response->viewData('active'));    // applied + interviewing
        $this->assertSame(1, $response->viewData('offers'));
        // 4 submitted (everything but the wishlist); interviewing + offer reached an interview = 2/4.
        $this->assertSame(50, $response->viewData('interviewRate'));
    }

    public function test_a_logged_interview_counts_even_if_status_was_not_updated(): void
    {
        $user = User::factory()->create();
        $application = JobApplication::factory()->for($user)->create(['status' => ApplicationStatus::Applied]);
        Interview::factory()->for($application)->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $this->assertSame(100, $response->viewData('interviewRate'));
    }

    public function test_interview_rate_is_unknown_when_nothing_was_submitted(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['status' => ApplicationStatus::Wishlist]);

        $this->actingAs($user)->get('/dashboard')
            ->assertOk()
            ->assertViewHas('interviewRate', null);
    }

    public function test_only_future_interviews_of_the_current_user_are_listed(): void
    {
        $user = User::factory()->create();
        $mine = JobApplication::factory()->for($user)->create(['company' => 'Mine Soon Ltd']);
        $past = JobApplication::factory()->for($user)->create(['company' => 'Mine Past Ltd']);
        Interview::factory()->for($mine)->create(['scheduled_at' => now()->addDays(2)]);
        Interview::factory()->for($past)->create(['scheduled_at' => now()->subDays(2)]);
        Interview::factory()->create(['scheduled_at' => now()->addDays(2)]); // someone else's

        $response = $this->actingAs($user)->get('/dashboard')->assertOk();

        $this->assertCount(1, $response->viewData('upcomingInterviews'));
        $response->assertSee('Mine Soon Ltd')->assertDontSee('Mine Past Ltd');
    }

    public function test_follow_ups_due_lists_only_open_overdue_applications(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create([
            'company' => 'Chase Me Ltd', 'status' => ApplicationStatus::Applied, 'follow_up_on' => now()->subDay(),
        ]);
        JobApplication::factory()->for($user)->create([
            'company' => 'Later Ltd', 'status' => ApplicationStatus::Applied, 'follow_up_on' => now()->addWeek(),
        ]);
        JobApplication::factory()->for($user)->create([
            'company' => 'Closed Ltd', 'status' => ApplicationStatus::Rejected, 'follow_up_on' => now()->subDay(),
        ]);

        $this->actingAs($user)->get('/dashboard')
            ->assertSee('Chase Me Ltd')
            ->assertDontSee('Later Ltd')
            ->assertDontSee('Closed Ltd');
    }

    public function test_weekly_chart_covers_eight_weeks_and_counts_this_week(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->count(2)->create(['applied_on' => today()]);
        JobApplication::factory()->for($user)->create(['applied_on' => today()->subWeeks(20)]); // too old

        $weekly = $this->actingAs($user)->get('/dashboard')->viewData('chartData')['weekly'];

        $this->assertCount(8, $weekly['values']);
        $this->assertSame(2, end($weekly['values']));
        $this->assertSame(2, array_sum($weekly['values']));
    }
}
