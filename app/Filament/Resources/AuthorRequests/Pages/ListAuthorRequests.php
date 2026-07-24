<?php

namespace App\Filament\Resources\AuthorRequests\Pages;

use App\Filament\Resources\AuthorRequests\AuthorRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListAuthorRequests extends ListRecords
{
    protected static string $resource = AuthorRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
