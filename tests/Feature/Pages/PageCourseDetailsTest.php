<?php

use App\Models\Course;
use App\Models\Video;
use function Pest\Laravel\get;


it('does not find unreleased course', function () {
    // Arrange
    $course = Course::factory()->create();

    // Act & Assert
    get(route('pages.course-details', $course))
        ->assertNotFound();
});

it('shows course details', function () {
    // Arrange
    $course = Course::factory()->released()->create();

    // Act & Assert
    get(route('pages.course-details', $course))
        ->assertOk()
        ->assertSeeText([
            $course->title,
            $course->description,
            $course->tagline,
            ...$course->learnings,
        ])
        ->assertSee(asset("images/{$course->image_name}"));
});

it('shows course video count', function () {
    // Arrange
    $course = Course::factory()
                    ->released()
                    ->has(Video::factory()->count(3))
                    ->create();

    // Act & Assert
    get(route('pages.course-details', $course))
        ->assertOk()
        ->assertSeeText('3 videos');
});

it('includes paddle checkout button', function () {
    // Arrange
    $course = Course::factory()->released()->create([
        'paddle_product_id'=>'product_id',
    ]);

    // Act & Assert
    get(route('pages.course-details', $course))
        ->assertOk()
        ->assertSee('<script src="https://cdn.paddle.com/paddle/paddle.js"></script>', false)
        ->assertSee('Paddle.Setup({vendor: vendor-id})', false)
        ->assertSee('<a href="#!" class="paddle_button" data-product="product_id">Buy Now</a>', false);
});
