<?php

namespace App\Filament\Resources\AuthorRequests\Tables;

use App\Models\AuthorRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class AuthorRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable()
                    ->description(fn (AuthorRequest $record): string => $record->user->email),

                TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(80)
                    ->wrap(),

                TextColumn::make('portfolio_url')
                    ->label('Portofolio')
                    ->limit(40)
                    ->url(fn (AuthorRequest $record): ?string => $record->portfolio_url)
                    ->openUrlInNewTab()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => AuthorRequest::statusOptions()[$state] ?? $state),

                TextColumn::make('reviewer.name')
                    ->label('Ditinjau oleh')
                    ->placeholder('-'),

                TextColumn::make('reviewed_at')
                    ->label('Tgl. Ditinjau')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Tgl. Dikirim')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(AuthorRequest::statusOptions())
                    ->native(false),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon(Heroicon::OutlinedCheck)
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Permintaan Author')
                    ->modalDescription(fn (AuthorRequest $record): string => 'Setujui permintaan dari '.$record->user->name.'? User akan otomatis mendapat role "author".')
                    ->modalSubmitActionLabel('Ya, Setujui')
                    ->visible(fn (AuthorRequest $record): bool => $record->status !== 'approved')
                    ->action(function (AuthorRequest $record): void {
                        $record->update([
                            'status' => 'approved',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                            'admin_notes' => null,
                        ]);
                        $record->user->assignRole('author');
                    })
                    ->successNotificationTitle('Permintaan disetujui dan role author diberikan.'),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->schema([
                        Textarea::make('admin_notes')
                            ->label('Alasan Penolakan')
                            ->placeholder('Tuliskan alasan penolakan untuk diberitahukan ke pemohon...')
                            ->rows(3),
                    ])
                    ->modalHeading('Tolak Permintaan Author')
                    ->modalSubmitActionLabel('Tolak Permintaan')
                    ->visible(fn (AuthorRequest $record): bool => $record->status !== 'rejected')
                    ->action(function (AuthorRequest $record, array $data): void {
                        if ($record->status === 'approved') {
                            $record->user->removeRole('author');
                        }
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_by' => Auth::id(),
                            'reviewed_at' => now(),
                            'admin_notes' => $data['admin_notes'] ?? null,
                        ]);
                    })
                    ->successNotificationTitle('Permintaan ditolak.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
