<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_ReturnTheLoggedInUser()
    {
        $response = $this->actingAs($this->user)->json('GET', route('user'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
            ]);
    }

    /**
     * @test
     */
    public function should_ReturnsEmptyStringIfNotLoggedIn()
    {
        $response = $this->json('GET', route('user'));

        $response->assertStatus(200);
        $this->assertEquals("", $response->content());
    }
}
