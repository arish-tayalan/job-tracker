<?php

namespace Tests\Feature;

use App\Models\Interview;
use App\Models\JobApplication;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_demo_account_with_data(): void
    {
        $this->seed(DemoSeeder::class);

        $demo = User::where('email', DemoSeeder::EMAIL)->firstOrFail();

        $this->assertSame(12, $demo->jobApplications()->count());
        $this->assertGreaterThan(0, Interview::count());
        $this->assertTrue(password_verify(DemoSeeder::PASSWORD, $demo->password));
    }

    public function test_running_it_twice_resets_instead_of_duplicating(): void
    {
        $this->seed(DemoSeeder::class);
        $this->seed(DemoSeeder::class);

        $this->assertSame(1, User::where('email', DemoSeeder::EMAIL)->count());
        $this->assertSame(12, JobApplication::count());
    }

    public function test_it_leaves_other_users_alone(): void
    {
        $other = JobApplication::factory()->create(['company' => 'Keep Me Ltd']);

        $this->seed(DemoSeeder::class);

        $this->assertModelExists($other);
        $this->assertSame(13, JobApplication::count());
    }

    public function test_demo_user_can_log_in_and_see_the_dashboard_with_data(): void
    {
        $this->seed(DemoSeeder::class);

        $this->post('/login', ['email' => DemoSeeder::EMAIL, 'password' => DemoSeeder::PASSWORD])
            ->assertRedirect('/dashboard');

        $response = $this->get('/dashboard')->assertOk();

        $this->assertSame(12, $response->viewData('total'));
        $this->assertSame(1, $response->viewData('offers'));
        $this->assertGreaterThan(0, $response->viewData('upcomingInterviews')->count());
        $this->assertGreaterThan(0, $response->viewData('followUps')->count());
    }
}
