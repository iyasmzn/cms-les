<?php

namespace App\Filament\Resources\AuthorRequests\Pages;

use App\Filament\Resources\AuthorRequests\AuthorRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAuthorRequest extends CreateRecord
{
    protected static string $resource = AuthorRequestResource::class;
}
