<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobApplicationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/applications')->assertRedirect('/login');
    }

    public function test_user_sees_only_their_own_applications(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['company' => 'Mine Sdn Bhd']);
        JobApplication::factory()->create(['company' => 'Someone Else Ltd']);

        $this->actingAs($user)->get('/applications')
            ->assertOk()
            ->assertSee('Mine Sdn Bhd')
            ->assertDontSee('Someone Else Ltd');
    }

    public function test_list_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['company' => 'Offer Co', 'status' => ApplicationStatus::Offer]);
        JobApplication::factory()->for($user)->create(['company' => 'Rejected Co', 'status' => ApplicationStatus::Rejected]);

        $this->actingAs($user)->get('/applications?status=offer')
            ->assertOk()
            ->assertSee('Offer Co')
            ->assertDontSee('Rejected Co');
    }

    public function test_list_can_be_searched_by_company_or_position(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['company' => 'Google', 'position' => 'Intern']);
        JobApplication::factory()->for($user)->create(['company' => 'Acme', 'position' => 'Google Cloud Dev']);
        JobApplication::factory()->for($user)->create(['company' => 'Initech', 'position' => 'Tester']);

        $this->actingAs($user)->get('/applications?search=google')
            ->assertOk()
            ->assertSee('Intern')
            ->assertSee('Acme')
            ->assertDontSee('Initech');
    }

    public function test_search_never_shows_other_users_applications(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['company' => 'Mine Google']);
        JobApplication::factory()->create(['company' => 'Their Google']);

        $this->actingAs($user)->get('/applications?search=google')
            ->assertSee('Mine Google')
            ->assertDontSee('Their Google');
    }

    public function test_list_can_be_sorted_by_company(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create(['company' => 'Bravo Ltd']);
        JobApplication::factory()->for($user)->create(['company' => 'Alpha Ltd']);
        JobApplication::factory()->for($user)->create(['company' => 'Charlie Ltd']);

        $this->actingAs($user)->get('/applications?sort=company&dir=asc')
            ->assertSeeInOrder(['Alpha Ltd', 'Bravo Ltd', 'Charlie Ltd']);

        $this->actingAs($user)->get('/applications?sort=company&dir=desc')
            ->assertSeeInOrder(['Charlie Ltd', 'Bravo Ltd', 'Alpha Ltd']);
    }

    public function test_unknown_sort_column_is_ignored(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create();

        $this->actingAs($user)->get('/applications?sort=password;drop&dir=sideways')
            ->assertOk();
    }

    public function test_user_can_view_their_application_details(): void
    {
        $application = JobApplication::factory()->create([
            'company' => 'Detail Co',
            'notes' => 'Spoke with Sarah from HR',
        ]);

        $this->actingAs($application->user)->get("/applications/{$application->id}")
            ->assertOk()
            ->assertSee('Detail Co')
            ->assertSee('Spoke with Sarah from HR');
    }

    public function test_user_cannot_view_someone_elses_application(): void
    {
        $application = JobApplication::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get("/applications/{$application->id}")
            ->assertForbidden();
    }

    public function test_user_can_add_an_interview_to_their_application(): void
    {
        $application = JobApplication::factory()->create();

        $this->actingAs($application->user)->post("/applications/{$application->id}/interviews", [
            'scheduled_at' => '2026-11-03T14:30',
            'type' => 'technical',
            'notes' => 'Live coding with Sarah',
        ])->assertRedirect("/applications/{$application->id}");

        $this->assertDatabaseHas('interviews', [
            'job_application_id' => $application->id,
            'type' => 'technical',
            'notes' => 'Live coding with Sarah',
        ]);

        $this->actingAs($application->user)->get("/applications/{$application->id}")
            ->assertSee('Live coding with Sarah');
    }

    public function test_interview_requires_valid_data(): void
    {
        $application = JobApplication::factory()->create();

        $this->actingAs($application->user)->post("/applications/{$application->id}/interviews", [
            'scheduled_at' => 'not a date',
            'type' => 'chat',
        ])->assertSessionHasErrors(['scheduled_at', 'type']);
    }

    public function test_user_can_delete_an_interview(): void
    {
        $interview = Interview::factory()->create();

        $this->actingAs($interview->jobApplication->user)->delete("/interviews/{$interview->id}");

        $this->assertModelMissing($interview);
    }

    public function test_user_cannot_touch_interviews_of_someone_elses_application(): void
    {
        $interview = Interview::factory()->create();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->post("/applications/{$interview->job_application_id}/interviews", [
            'scheduled_at' => '2026-11-03T14:30', 'type' => 'phone',
        ])->assertForbidden();
        $this->actingAs($intruder)->delete("/interviews/{$interview->id}")->assertForbidden();

        $this->assertModelExists($interview);
        $this->assertDatabaseCount('interviews', 1);
    }

    public function test_deleting_an_application_deletes_its_interviews(): void
    {
        $interview = Interview::factory()->create();

        $this->actingAs($interview->jobApplication->user)
            ->delete("/applications/{$interview->job_application_id}");

        $this->assertModelMissing($interview);
    }

    public function test_follow_up_due_is_flagged_only_for_open_applications(): void
    {
        $user = User::factory()->create();
        JobApplication::factory()->for($user)->create([
            'company' => 'Overdue Co', 'status' => ApplicationStatus::Applied, 'follow_up_on' => now()->subDay(),
        ]);
        JobApplication::factory()->for($user)->create([
            'company' => 'Future Co', 'status' => ApplicationStatus::Applied, 'follow_up_on' => now()->addWeek(),
        ]);
        JobApplication::factory()->for($user)->create([
            'company' => 'Closed Co', 'status' => ApplicationStatus::Rejected, 'follow_up_on' => now()->subDay(),
        ]);

        $html = $this->actingAs($user)->get('/applications')->getContent();

        $this->assertSame(1, substr_count($html, 'Follow up due'));
    }

    public function test_user_can_create_an_application(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/applications', [
            'company' => 'Acme',
            'position' => 'Junior Developer',
            'status' => 'applied',
            'applied_on' => now()->toDateString(),
        ])->assertRedirect('/applications');

        $this->assertDatabaseHas('job_applications', [
            'user_id' => $user->id,
            'company' => 'Acme',
            'status' => 'applied',
        ]);
    }

    public function test_invalid_data_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/applications', [
            'company' => '',
            'status' => 'not-a-status',
            'job_url' => 'javascript:alert(1)',
        ])->assertSessionHasErrors(['company', 'position', 'status', 'job_url']);
    }

    public function test_user_can_update_their_application(): void
    {
        $application = JobApplication::factory()->create(['status' => ApplicationStatus::Applied]);

        $this->actingAs($application->user)->put("/applications/{$application->id}", [
            'company' => $application->company,
            'position' => $application->position,
            'status' => 'interviewing',
        ])->assertRedirect('/applications');

        $this->assertSame(ApplicationStatus::Interviewing, $application->fresh()->status);
    }

    public function test_user_can_delete_their_application(): void
    {
        $application = JobApplication::factory()->create();

        $this->actingAs($application->user)->delete("/applications/{$application->id}");

        $this->assertModelMissing($application);
    }

    public function test_user_cannot_edit_or_delete_someone_elses_application(): void
    {
        $application = JobApplication::factory()->create();
        $intruder = User::factory()->create();

        $this->actingAs($intruder)->get("/applications/{$application->id}/edit")->assertForbidden();
        $this->actingAs($intruder)->put("/applications/{$application->id}", [
            'company' => 'Hacked', 'position' => 'x', 'status' => 'offer',
        ])->assertForbidden();
        $this->actingAs($intruder)->delete("/applications/{$application->id}")->assertForbidden();

        $this->assertModelExists($application);
    }
}
