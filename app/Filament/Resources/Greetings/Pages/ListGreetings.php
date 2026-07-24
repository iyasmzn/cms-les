<?php

namespace App\Filament\Resources\Greetings\Pages;

use App\Filament\Resources\Greetings\GreetingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGreetings extends ListRecords
{
    protected static string $resource = GreetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
