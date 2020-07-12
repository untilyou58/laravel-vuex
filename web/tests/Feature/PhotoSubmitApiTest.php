<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_CanUploadFiles()
    {
        // Use storage instead of S3
        // → storage/framework/testing
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                // Create a fake photo then send it
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // Response 201 status (CREATED)
        $response->assertStatus(201);

        $photo = Photo::first();

        // ID of image is 12 digit random string
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        // The file with the file name inserted in DB is saved in storage
        Storage::cloud()->assertExists($photo->filename);
    }

    /**
     * @test
     */
    public function should_DoNotSaveFileInCaseOfDatabaseError()
    {
        // It is roughly but it causes DB error
        Schema::drop('photos');

        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // Response 500 error (INTERNAL SERVER ERROR)
        $response->assertStatus(500);

        // No file stored in storage
        $this->assertEquals(0, count(Storage::cloud()->files()));
    }

    /**
     * @test
     */
    public function should_InCaseOfFileSaveErrorDoNotInsertIntoDB()
    {
        // Mock the storage and cause an error when saving
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                // Create a fake file and send it
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // Response 500 error (INTERNAL SERVER ERROR)
        $response->assertStatus(500);

        // Nothing is inserted in the database
        $this->assertEmpty(Photo::all());
    }
}
