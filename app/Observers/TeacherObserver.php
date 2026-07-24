<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Services\MediaLibraryService;

class TeacherObserver
{
    public function __construct(private MediaLibraryService $media) {}

    public function created(Teacher $teacher): void
    {
        if ($teacher->photo) {
            $this->media->sync($teacher->photo);
        }
    }

    public function updated(Teacher $teacher): void
    {
        if ($teacher->wasChanged('photo') && $teacher->photo) {
            $this->media->sync($teacher->photo);
        }
    }
}
