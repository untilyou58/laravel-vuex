<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PhotoListApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function should_ReturnJSONWithCorrectStructure()
    {
        // Generate 5 photo data
        factory(Photo::class, 5)->create();

        $response = $this->json('GET', route('photo.index'));

        // Acquire the generated photo data in descending order of creation date
        $photos = Photo::with(['owner'])->orderBy('created_at', 'desc')->get();

        // Expected value of data item
        $expected_data = $photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'owner' => [
                    'name' => $photo->owner->name,
                ],
                'liked_by_user' => false,
                'likes_count' => 0,
            ];
        })
        ->all();

        $response->assertStatus(200)
            // There must be 5 elements contained in the response JSON data item
            ->assertJsonCount(5, 'data')
            // The response JSON data item matches the expected value
            ->assertJsonFragment([
                "data" => $expected_data,
            ]);
    }
}
