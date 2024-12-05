<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('gives back succesful respond for home page', function () {
    get(route('home'))->assertOk(); //assert = afirmacion
});


it('gives back succesful respond of course details page', function () {
   //Arrange
    $course=Course::factory()->create();

    //Act
    get(route('course-details',$course))->assertOk();
});
