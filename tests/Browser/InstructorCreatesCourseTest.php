<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class InstructorCreatesCourseTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Test that an instructor can create a course.
     *
     * @return void
     */
    public function test_instructor_can_create_a_course(): void
    {
        $instructor = User::factory()->create(['user_type' => 'instructor']);

        $this->browse(function (Browser $browser) use ($instructor) {
            $browser->visit('/login')
                    ->type('email', $instructor->email)
                    ->type('password', 'password') // Default password for factory users
                    ->press('LOG IN')
                    ->assertPathIs('/dashboard')
                    ->clickLink('Create New Course')
                    ->assertPathIs('/courses/create')
                    ->type('title', 'My New Dusk Course')
                    ->type('description', 'A description created by Dusk.')
                    ->type('price', '123.45')
                    ->press('Create Course')
                    ->assertPathIsNot('/courses/create') // Ensure we've been redirected
                    ->screenshot('course-show-page')
                    ->assertSee('My New Dusk Course');
        });
    }
}