<?php

namespace App\Filament\Resources\CoursePayments\Pages;

use App\Filament\Resources\CoursePayments\CoursePaymentResource;
use App\Models\CoursePayment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;

class ViewCoursePayment extends ViewRecord
{
    protected static string $resource = CoursePaymentResource::class;

    /**
     * Deliberately minimal: change the status, or open the full form. Opening
     * a payment lands here rather than straight in the form, so reviewing a
     * member's proof can't turn into an accidental edit.
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->changeStatusAction(),
            EditAction::make(),
        ];
    }

    /**
     * One button for the whole status flow: verifying a confirmation (paid),
     * sending it back with a reason (unpaid), or waiving the bill.
     */
    private function changeStatusAction(): Action
    {
        return Action::make('changeStatus')
            ->label('Change status')
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('primary')
            ->modalHeading('Change payment status')
            ->modalSubmitActionLabel('Save status')
            ->fillForm(fn (CoursePayment $record): array => [
                'status' => $record->status,
                'method' => $record->method ?? 'cash',
            ])
            ->schema([
                Select::make('status')
                    ->label('Status')
                    ->options(CoursePayment::statusOptions())
                    ->required()
                    ->native(false)
                    ->live(),

                Select::make('method')
                    ->label('Method')
                    ->options(CoursePayment::methodOptions())
                    ->native(false)
                    ->required()
                    ->visible(fn (Get $get): bool => $get('status') === 'paid'),

                TextInput::make('reason')
                    ->label('Reason')
                    ->maxLength(255)
                    ->placeholder('e.g. Transfer amount does not match the bill')
                    ->helperText('Shown to the member in their portal so they know what to fix.')
                    ->required()
                    ->visible(fn (Get $get, CoursePayment $record): bool => $get('status') === 'unpaid'
                        && $record->isAwaitingVerification()),
            ])
            ->action(function (array $data, CoursePayment $record): void {
                match ($data['status']) {
                    'paid' => $record->markPaid($data['method'] ?? null),
                    'unpaid' => $record->isAwaitingVerification()
                        ? $record->reject($data['reason'] ?? null)
                        : $record->update(['status' => 'unpaid', 'paid_at' => null]),
                    default => $record->update(['status' => $data['status']]),
                };

                Notification::make()
                    ->success()
                    ->title('Status updated')
                    ->body('Now '.(CoursePayment::statusOptions()[$record->fresh()->status] ?? $record->status).'.')
                    ->send();
            });
    }
}
