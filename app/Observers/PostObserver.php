<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\MediaLibraryService;

class PostObserver
{
    public function __construct(private MediaLibraryService $media) {}

    public function created(Post $post): void
    {
        if ($post->image) {
            $this->media->sync($post->image);
        }
    }

    public function updated(Post $post): void
    {
        if ($post->wasChanged('image') && $post->image) {
            $this->media->sync($post->image);
        }
    }
}
