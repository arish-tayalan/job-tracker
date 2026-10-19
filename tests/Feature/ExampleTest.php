<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_guests_see_login_and_signup_links_on_the_landing_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Log in')
            ->assertSee('Get started')
            ->assertDontSee('My applications');
    }

    public function test_logged_in_users_see_a_link_to_their_applications(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertSee('Go to my applications')
            ->assertDontSee('Get started');
    }
}
