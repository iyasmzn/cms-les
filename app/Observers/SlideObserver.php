<?php

namespace App\Observers;

use App\Models\Slide;
use App\Services\MediaLibraryService;

class SlideObserver
{
    public function __construct(private MediaLibraryService $media) {}

    public function created(Slide $slide): void
    {
        if ($slide->image) {
            $this->media->sync($slide->image);
        }
    }

    public function updated(Slide $slide): void
    {
        if ($slide->wasChanged('image') && $slide->image) {
            $this->media->sync($slide->image);
        }
    }
}
