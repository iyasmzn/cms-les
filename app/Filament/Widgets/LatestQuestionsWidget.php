<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Question;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestQuestionsWidget extends TableWidget
{
    use HasWidgetShield;

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Pertanyaan Belum Dijawab';

    public static function canView(): bool
    {
        return parent::canView() && feature_enabled('pertanyaan');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lihat_semua')
                ->label('Kelola Pertanyaan')
                ->url(QuestionResource::getUrl('index'))
                ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                ->color('gray')
                ->size('sm'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Question::query()
                ->where('is_answered', false)
                ->orderByDesc('created_at')
                ->limit(8)
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Pengirim')
                    ->searchable(),

                TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->limit(80)
                    ->wrap(),

                IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
