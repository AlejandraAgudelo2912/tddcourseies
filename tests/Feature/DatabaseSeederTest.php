<?php

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;


it('adds given courses', function () {
    //Arrange
    $this->assertDatabaseCount(Course::class, 0);

    //Act
    $this->artisan('db:seed');

    //Assert
    $this->assertDatabaseCount(Course::class, 3);
    $this->assertDatabaseHas(Course::class, ['title' => 'Laravel For Beginners']);
    $this->assertDatabaseHas(Course::class, ['title' => 'Advanced Laravel']);
    $this->assertDatabaseHas(Course::class, ['title' => 'TDD the Laravel Way']);

});

it('adds given courses only once', function () {
    //Act
    $this->artisan('db:seed');
    $this->artisan('db:seed');

    //Assert
    $this->assertDatabaseCount(Course::class, 3);
});
