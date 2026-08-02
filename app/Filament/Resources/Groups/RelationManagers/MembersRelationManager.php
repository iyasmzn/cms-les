<?php

namespace App\Filament\Resources\Groups\RelationManagers;

use App\Models\Group;
use App\Models\GroupMember;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Members';

    protected static ?string $modelLabel = 'Member';

    protected static ?string $pluralModelLabel = 'Members';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(120),

                Select::make('gender')
                    ->label('Gender')
                    ->options([
                        'male' => 'Male',
                        'female' => 'Female',
                    ])
                    ->native(false),
            ]),

            Grid::make(2)->schema([
                DatePicker::make('birth_date')
                    ->label('Birth Date'),

                TextInput::make('birth_place')
                    ->label('Birth Place')
                    ->maxLength(120),
            ]),

            Grid::make(2)->schema([
                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->maxLength(30),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(120),
            ]),

            Textarea::make('address')
                ->label('Address')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('parent_name')
                    ->label('Parent/Guardian Name')
                    ->maxLength(120),

                TextInput::make('parent_phone')
                    ->label('Parent/Guardian Phone')
                    ->tel()
                    ->maxLength(30),
            ]),

            Grid::make(2)->schema([
                Select::make('status')
                    ->label('Status')
                    ->options(GroupMember::statusOptions())
                    ->default('pending')
                    ->required()
                    ->native(false),

                DatePicker::make('joined_at')
                    ->label('Joined At'),
            ]),

            Textarea::make('notes')
                ->label('Notes')
                ->rows(2)
                ->maxLength(1000)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('registration_number')
                    ->label('Reg. No.')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->placeholder('—'),

                TextColumn::make('user.name')
                    ->label('Account')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn (?string $state): string => $state ?? 'Guest')
                    ->default('Guest'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => GroupMember::statusOptions()[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        default => 'warning',
                    }),

                TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(GroupMember::statusOptions()),
            ])
            ->headerActions([
                CreateAction::make()->label('Add Member'),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (GroupMember $record): bool => $record->status !== 'active')
                    ->action(function (GroupMember $record): void {
                        $record->update([
                            'status' => 'active',
                            'joined_at' => $record->joined_at ?? now(),
                        ]);
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (GroupMember $record): bool => $record->status !== 'inactive')
                    ->action(fn (GroupMember $record) => $record->update(['status' => 'inactive'])),

                Action::make('moveGroup')
                    ->label('Move Group')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('info')
                    ->modalHeading('Move Member to Another Group')
                    ->modalDescription('Registration number, join date, and payment history all follow the member.')
                    ->visible(fn (): bool => $this->canMoveMembers())
                    ->schema([
                        Select::make('group_id')
                            ->label('Move to')
                            ->options(fn (): array => $this->moveTargets())
                            ->required()
                            ->native(false)
                            ->helperText('Active groups in this same course that still have seats.'),
                    ])
                    ->action(fn (GroupMember $record, array $data) => $this->moveMember($record, (int) $data['group_id'])),

                EditAction::make(),

                DeleteAction::make()
                    ->visible(fn (): bool => auth()->user()?->can('Delete:Group') ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => auth()->user()?->can('DeleteAny:Group') ?? false),
                ]),
            ]);
    }

    /**
     * Whether the current user may move members out of the group being viewed.
     */
    protected function canMoveMembers(): bool
    {
        return auth()->user()?->can('moveMember', $this->getOwnerRecord()) ?? false;
    }

    /**
     * Sibling groups a member can be moved into: same course, still active,
     * and not already full. Keyed by id for the Select.
     *
     * @return array<int, string>
     */
    protected function moveTargets(): array
    {
        $current = $this->getOwnerRecord();

        return Group::query()
            ->where('institution_id', $current->institution_id)
            ->whereKeyNot($current->getKey())
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn (Group $group): bool => $group->isOpen())
            ->mapWithKeys(fn (Group $group): array => [
                $group->id => $group->level ? "{$group->name} — {$group->level}" : $group->name,
            ])
            ->all();
    }

    /**
     * Move the member, re-checking the target group at the moment of the write
     * so a group that filled up while the modal was open is not overfilled.
     */
    protected function moveMember(GroupMember $member, int $targetGroupId): void
    {
        $current = $this->getOwnerRecord();
        $target = Group::find($targetGroupId);

        $refuse = function (string $body): void {
            Notification::make()->danger()->title('Move failed')->body($body)->send();
        };

        if ($target === null || $target->institution_id !== $current->institution_id || ! $target->is_active) {
            $refuse('That group is not part of this course.');

            return;
        }

        if (! $target->isOpen()) {
            $refuse("\"{$target->name}\" is full.");

            return;
        }

        // A member may only hold one group per course, so a stale duplicate in
        // the target group has to be resolved by hand rather than merged here.
        $duplicate = $target->members()
            ->whereIn('status', ['pending', 'active'])
            ->when(
                $member->user_id,
                fn ($query) => $query->where('user_id', $member->user_id),
                fn ($query) => $query->where('phone', $member->phone),
            )
            ->exists();

        if ($duplicate) {
            $refuse("This member already has a registration in \"{$target->name}\".");

            return;
        }

        $member->update(['group_id' => $target->id]);

        Notification::make()
            ->success()
            ->title('Member moved')
            ->body("{$member->full_name} is now in \"{$target->name}\".")
            ->send();
    }
}
