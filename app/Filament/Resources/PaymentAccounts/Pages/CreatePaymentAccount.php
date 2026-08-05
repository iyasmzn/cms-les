<?php

namespace App\Filament\Resources\PaymentAccounts\Pages;

use App\Filament\Resources\PaymentAccounts\PaymentAccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentAccount extends CreateRecord
{
    protected static string $resource = PaymentAccountResource::class;
}
