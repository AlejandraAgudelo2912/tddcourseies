<div>
    <iframe src="https://player.vimeo.com/video/{{ $video->vimeo_id }}"
            webkitallowfullscreen mozallowfullscreen allowfullscreen
    ></iframe>
    <h3>{{ $video->title }} ({{ $video->getReadableDuration() }})</h3>
    <p>{{ $video->description }}</p>

    @if($video->alreadyWatchedByCurrentUser())
        <button wire:click="markAsNotCompleted">Mark as not completed</button>
    @else
        <button wire:click="markAsCompleted">Mark as completed</button>
    @endif
</div>
<ul>
    @foreach($courseVideos as $courseVideo)
        <li>
            @if($this->isCurrentVideo($courseVideo))
                {{ $courseVideo->title }}
            @else
            <a href="{{ route('pages.course-videos', $courseVideo) }}">
                {{ $courseVideo->title }}
            </a>
            @endif
        </li>
    @endforeach
</ul>
