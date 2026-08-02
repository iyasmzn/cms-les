<?php

namespace App\Filament\Resources\Groups;

use App\Filament\Resources\Groups\Pages\CreateGroup;
use App\Filament\Resources\Groups\Pages\EditGroup;
use App\Filament\Resources\Groups\Pages\ListGroups;
use App\Filament\Resources\Groups\RelationManagers\MembersRelationManager;
use App\Filament\Resources\Groups\RelationManagers\SessionsRelationManager;
use App\Filament\Resources\Groups\Schemas\GroupForm;
use App\Filament\Resources\Groups\Tables\GroupsTable;
use App\Models\Group;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Courses';

    protected static ?string $navigationLabel = 'Groups';

    protected static ?string $modelLabel = 'Group';

    protected static ?string $pluralModelLabel = 'Groups';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return GroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GroupsTable::configure($table);
    }

    /**
     * Accounts linked to a coach profile only see the groups they coach,
     * unless they hold ViewAll:Group. Everyone else sees the full list.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = auth()->user();

        if ($user instanceof User && $user->isInstructor() && ! $user->can('ViewAll:Group')) {
            $query->whereKey($user->coachedGroupIds());
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [
            SessionsRelationManager::class,
            MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGroups::route('/'),
            'create' => CreateGroup::route('/create'),
            'edit' => EditGroup::route('/{record}/edit'),
        ];
    }
}
