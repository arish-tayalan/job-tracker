<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/board')->assertRedirect('/login');
    }

    public function test_board_groups_the_users_applications_by_status(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['company' => 'Applied Co', 'status' => ApplicationStatus::Applied]);
        JobApplication::factory()->for($user)->create(['company' => 'Offer Co', 'status' => ApplicationStatus::Offer]);
        JobApplication::factory()->create(['company' => 'Stranger Co']);

        $response = $this->actingAs($user)->get('/board')->assertOk();

        $response->assertSee('Applied Co')->assertSee('Offer Co')->assertDontSee('Stranger Co');
        $this->assertCount(1, $response->viewData('columns')['applied']);
        $this->assertCount(1, $response->viewData('columns')['offer']);
    }

    public function test_moving_a_card_updates_the_status(): void
    {
        $application = JobApplication::factory()->create(['status' => ApplicationStatus::Applied]);

        $this->actingAs($application->user)
            ->patchJson("/applications/{$application->id}/status", ['status' => 'interviewing'])
            ->assertOk()
            ->assertJson(['status' => 'interviewing']);

        $this->assertSame(ApplicationStatus::Interviewing, $application->fresh()->status);
    }

    public function test_moving_a_card_rejects_an_unknown_status(): void
    {
        $application = JobApplication::factory()->create(['status' => ApplicationStatus::Applied]);

        $this->actingAs($application->user)
            ->patchJson("/applications/{$application->id}/status", ['status' => 'hired-by-google'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');

        $this->assertSame(ApplicationStatus::Applied, $application->fresh()->status);
    }

    public function test_user_cannot_move_someone_elses_card(): void
    {
        $application = JobApplication::factory()->create(['status' => ApplicationStatus::Applied]);

        $this->actingAs(User::factory()->create())
            ->patchJson("/applications/{$application->id}/status", ['status' => 'offer'])
            ->assertForbidden();

        $this->assertSame(ApplicationStatus::Applied, $application->fresh()->status);
    }
}
