<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an instructor can have many courses.
     *
     * @return void
     */
    public function test_instructor_can_have_many_courses(): void
    {
        $instructor = User::factory()->create(['user_type' => 'instructor']);
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        $this->assertInstanceOf(Course::class, $instructor->courses->first());
        $this->assertTrue($instructor->courses->contains($course));
    }
}