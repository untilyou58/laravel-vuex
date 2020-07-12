<?php

namespace Tests\Feature;

use App\Comment;
use App\Photo;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PhotoDetailApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_ReturnJSONWithCorrectStructure()
    {
        factory(Photo::class)->create()->each(function ($photo) {
            $photo->comments()->saveMany(factory(Comment::class, 3)->make());
        });
        $photo = Photo::first();

        $response = $this->json('GET', route('photo.show', [
            'id' => $photo->id,
        ]));

        $expected_data_comment = $photo->comments
        ->sortByDesc('id')
        ->map(function ($comment) {
            return [
                'author' => [
                    'name' => $comment->author->name,
                ],
                'content' => $comment->content,
            ];
        })
        ->all();

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name,
                ],
                'comments' => $expected_data_comment,
                'liked_by_user' => false,
                'likes_count' => 0,
            ]);
    }
}
