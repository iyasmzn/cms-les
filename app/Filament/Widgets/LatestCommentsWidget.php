<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Comments\CommentResource;
use App\Models\Comment;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestCommentsWidget extends TableWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Komentar Perlu Moderasi';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat_semua')
                ->label('Kelola Komentar')
                ->url(CommentResource::getUrl('index'))
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->size('sm'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Comment::query()
                ->with(['user', 'post'])
                ->where('is_approved', false)
                ->orderByDesc('created_at')
                ->limit(8)
            )
            ->columns([
                TextColumn::make('author_name')
                    ->label('Pengguna')
                    ->description(fn (Comment $record): ?string => $record->is_guest ? 'Tamu' : null)
                    ->state(fn (Comment $record): string => $record->author_name),

                TextColumn::make('post.title')
                    ->label('Artikel')
                    ->limit(30)
                    ->placeholder('—'),

                TextColumn::make('body')
                    ->label('Komentar')
                    ->limit(80)
                    ->wrap(),

                IconColumn::make('admin_reply')
                    ->label('Dibalas')
                    ->boolean()
                    ->state(fn (Comment $record): bool => filled($record->admin_reply)),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
