<?php

namespace App\Filament\Resources\Institutions\RelationManagers;

use App\Models\Institution;
use App\Models\PpdbField;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class PpdbFieldsRelationManager extends RelationManager
{
    protected static string $relationship = 'ppdbFields';

    protected static ?string $title = 'Field Formulir';

    protected static ?string $modelLabel = 'Field';

    protected static ?string $pluralModelLabel = 'Field';

    /**
     * The field builder only applies to jenjang that use the internal form.
     */
    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord instanceof Institution && $ownerRecord->usesInternalForm();
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('label')
                ->label('Label Field')
                ->required()
                ->maxLength(120)
                ->live(onBlur: true)
                ->afterStateUpdated(function (Set $set, Get $get, ?string $state): void {
                    if (blank($get('key'))) {
                        $set('key', Str::slug($state ?? '', '_'));
                    }
                })
                ->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('key')
                    ->label('Key (nama teknis)')
                    ->required()
                    ->maxLength(60)
                    ->rule('regex:/^[a-z][a-z0-9_]*$/')
                    ->unique(
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where('institution_id', $this->getOwnerRecord()->getKey()),
                        ignoreRecord: true,
                    )
                    ->helperText('Huruf kecil & garis bawah. Cocokkan dengan kolom pendaftar (mis. full_name, nik, phone) agar tersimpan ke kolom itu; selain itu masuk ke Data Tambahan.'),

                Select::make('type')
                    ->label('Tipe')
                    ->options(PpdbField::typeOptions())
                    ->default('text')
                    ->required()
                    ->native(false)
                    ->live(),
            ]),

            Repeater::make('options')
                ->label('Pilihan')
                ->schema([
                    TextInput::make('value')
                        ->label('Nilai')
                        ->required(),
                ])
                ->addActionLabel('+ Tambah Pilihan')
                ->visible(fn (Get $get): bool => in_array($get('type'), ['select', 'radio'], true))
                ->defaultItems(1)
                ->columnSpanFull(),

            Grid::make(2)->schema([
                TextInput::make('placeholder')
                    ->label('Placeholder')
                    ->maxLength(255),

                TextInput::make('help_text')
                    ->label('Teks Bantuan')
                    ->maxLength(255),
            ]),

            Grid::make(3)->schema([
                Select::make('width')
                    ->label('Lebar')
                    ->options(['full' => 'Penuh', 'half' => 'Setengah'])
                    ->default('full')
                    ->native(false),

                Toggle::make('is_required')
                    ->label('Wajib Diisi')
                    ->onColor('success'),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->onColor('success'),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order', 'asc')
            ->columns([
                TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('key')
                    ->label('Key')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => PpdbField::typeOptions()[$state] ?? $state),

                IconColumn::make('is_required')
                    ->label('Wajib')
                    ->boolean(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make()->label('Tambah Field'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
