<?php

namespace App\Filament\Resources\BillPaymentResource\Pages;

use App\Filament\Resources\BillPaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillPayments extends ListRecords
{
    protected static string $resource = BillPaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
