<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_CanAddComments()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();

        $content = 'sample content';

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.comment', [
                'photo' => $photo->id,
            ]), compact('content'));

        $comments = $photo->comments()->get();

        $response->assertStatus(201)
            // JSON format is as expected
            ->assertJsonFragment([
                "author" => [
                    "name" => $this->user->name,
                ],
                "content" => $content,
            ]);

        // 1 comment has been registered in the DB
        $this->assertEquals(1, $comments->count());
        // The content is requested by API
        $this->assertEquals($content, $comments[0]->content);
    }
}
