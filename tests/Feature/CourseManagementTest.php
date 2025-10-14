<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an instructor can create a course.
     *
     * @return void
     */
    public function test_instructor_can_create_a_course(): void
    {
        Storage::fake('public');

        $instructor = User::factory()->create(['user_type' => 'instructor']);
        $this->actingAs($instructor);

        $courseData = [
            'title' => 'New Course Title',
            'description' => 'This is a course description.',
            'price' => 99.99,
            'thumbnail' => UploadedFile::fake()->image('thumbnail.jpg'),
        ];

        $response = $this->post(route('courses.store'), $courseData);

        $this->assertDatabaseHas('courses', [
            'title' => 'New Course Title',
            'instructor_id' => $instructor->id,
        ]);

        $course = \App\Models\Course::first();
        $response->assertRedirect(route('courses.show', $course));

        Storage::disk('public')->assertExists('thumbnails/' . $courseData['thumbnail']->hashName());
    }

    /**
     * Test that a learner cannot create a course.
     *
     * @return void
     */
    public function test_learner_cannot_create_a_course(): void
    {
        $learner = User::factory()->create(['user_type' => 'learner']);
        $this->actingAs($learner);

        $courseData = [
            'title' => 'Learner Course Title',
            'description' => 'This should not be created.',
            'price' => 19.99,
        ];

        $response = $this->post(route('courses.store'), $courseData);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('courses', ['title' => 'Learner Course Title']);
    }
}