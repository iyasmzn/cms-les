<?php

namespace App\Filament\Resources\AuthorRequests\Schemas;

use App\Models\AuthorRequest;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AuthorRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pemohon')
                    ->schema([
                        Select::make('user_id')
                            ->label('Pemohon')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->required()
                            ->disabled(),

                        TextInput::make('portfolio_url')
                            ->label('Link Portofolio')
                            ->url()
                            ->disabled(),

                        Textarea::make('reason')
                            ->label('Alasan')
                            ->rows(5)
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Keputusan Admin')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(AuthorRequest::statusOptions())
                            ->required()
                            ->native(false),

                        Textarea::make('admin_notes')
                            ->label('Catatan Admin')
                            ->rows(3)
                            ->placeholder('Opsional — dikirim ke pemohon jika ditolak')
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }
}
