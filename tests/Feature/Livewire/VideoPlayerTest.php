<?php

use App\Http\Livewire\VideoPlayer;
use App\Models\Course;
use App\Models\Video;

function createCourseAndVideos(int $videosCount = 1): Course
{
    return Course::factory()
        ->has(Video::factory()->count($videosCount))
        ->create();
}

beforeEach(function () {
    $this->loggedInUser = loginAsUser();
});

it('shows details for given video', function () {
    // Arrange
    $course = createCourseAndVideos();

    // Act & Assert
    $video = $course->videos->first();
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertSeeText([
            $video->title,
            $video->description,
            "({$video->duration_in_min}min)",
        ]);

});

it('shows given video', function () {
    // Arrange
    $course = createCourseAndVideos();

    // Act & Assert
    $video = $course->videos->first();
    Livewire::test(VideoPlayer::class, ['video' => $video])
        ->assertSeeHtml('<iframe src="https://player.vimeo.com/video/'.$video->vimeo_id.'"');
});

it('shows list of all course videos', function () {
    // Arrange
    $course = createCourseAndVideos(3);

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos()->first()])
        ->assertSee(...$course->videos->pluck('title'))
        ->assertSeeHtml([
            route('pages.course-videos', ['course' => $course, 'video' => $course->videos[1]]),
            route('pages.course-videos', ['course' => $course, 'video' => $course->videos[2]]),
        ]);
});

it('marks video as completed', function () {
    // Arrange
    $course = createCourseAndVideos();

    $this->loggedInUser->purchasedCourses()->attach($course);

    //Assert
    expect($this->loggedInUser->watchedVideos)->toHaveCount(0);

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertMethodWired('markAsCompleted')
        ->call('markAsCompleted')
        ->assertMethodWired('markAsNotCompleted')
        ->assertMethodNotWired('markAsCompleted');

    //Assert
    $this->loggedInUser->refresh();
    expect($this->loggedInUser->watchedVideos)->toHaveCount(1)->first()->title->toEqual($course->videos->first()->title);
});

it('marks video as not completed', function () {
    // Arrange
    $course = createCourseAndVideos();

    $this->loggedInUser->purchasedCourses()->attach($course);
    $this->loggedInUser->watchedVideos()->attach($course->videos->first());

    // Assert
    expect($this->loggedInUser->watchedVideos)->toHaveCount(1);

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertMethodWired('markAsNotCompleted')
        ->call('markAsNotCompleted')
        ->assertMethodWired('markAsCompleted')
        ->assertMethodNotWired('markAsNotCompleted');

    // Assert
    $this->loggedInUser->refresh();
    expect($this->loggedInUser->watchedVideos)->toHaveCount(0);

});

it('does not include route for current video', function () {
    // Arrange
    $course = createCourseAndVideos();

    // Act & Assert
    Livewire::test(VideoPlayer::class, ['video' => $course->videos->first()])
        ->assertDontSeeHtml(route('pages.course-videos', $course->videos->first()));
});
