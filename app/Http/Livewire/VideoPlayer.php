<?php

namespace App\Http\Livewire;

use Livewire\Component;

class VideoPlayer extends Component
{
    public $video;

    public $courseVideos;

    public function mount(): void
    {
        $this->courseVideos = $this->video->course->videos;
    }

    public function markAsCompleted(): void
    {
        auth()->user()->watchedVideos()->attach($this->video->id);
    }

    public function markAsNotCompleted(): void
    {
        auth()->user()->watchedVideos()->detach($this->video->id);
    }
    public function render()
    {
        return view('livewire.video-player');
    }
}
