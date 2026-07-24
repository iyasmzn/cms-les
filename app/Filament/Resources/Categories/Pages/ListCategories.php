<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use App\Models\Category;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListCategories extends ListRecords
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Semua')
                ->badge(Category::count()),
        ];

        foreach (Category::TYPE_LABELS as $type => $label) {
            $tabs[$type] = Tab::make($label)
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $type))
                ->badge(Category::forType($type)->count());
        }

        return $tabs;
    }
}
