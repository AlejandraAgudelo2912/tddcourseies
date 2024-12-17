<?php

use App\Models\Course;
use App\Models\User;

use function Pest\Laravel\get;


it('gives back successful response for home page', function () {
    get(route('pages.home'))
        ->assertOk();
});

it('gives back successful response for course details page', function () {
    // Arrange
    $course = Course::factory()->released()->create();

    // Act
    get(route('pages.course-details', $course))
        ->assertOk();
});

it('gives back successful for dashboard page', function () {
    // Act & Assert
    loginAsUser();

    get(route('pages.dashboard'))
        ->assertOk();
});
