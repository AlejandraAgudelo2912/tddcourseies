<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('shows course details', function () {
    //Arrange
    $course = Course::factory()->create([
        'tagline'=>'Course Tagline',
        'image'=>'image.jpg',
        'learnings'=>[
            'Learn Laravel Routes',
            'Learn Laravel views',
            'Learn Laravel commands',
        ]
    ]);
    //Act
    get(route('course-details',$course))
    ->assertOk()
    ->assertSeeText([
        $course->title,
        $course->description,
        'Course Tagline',
        'Learn Laravel Routes',
        'Learn Laravel views',
        'Learn Laravel commands',
    ])
    ->assertSee('image.jpg');
    //Assert

});

it('shows course video count', function () {

});
