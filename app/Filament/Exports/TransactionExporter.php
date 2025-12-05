<?php

namespace App\Filament\Exports;

use App\Models\Transaction;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionExporter extends Exporter
{
    protected static ?string $model = Transaction::class;

    public static function getColumns(): array
    {
        return [
//            ExportColumn::make('created_at')
//                ->label('Date / Time'),

            ExportColumn::make('account.account_number')
                ->label('Account Number'),

            ExportColumn::make('type')
                ->label('Type'),

            ExportColumn::make('amount')
                ->label('Amount'),

            ExportColumn::make('currency')
                ->label('Currency'),

            ExportColumn::make('reference_no')
                ->label('Reference No.'),

            ExportColumn::make('status')
                ->label('Status'),

            ExportColumn::make('remarks')
                ->label('Remarks'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaction report has completed and '
            . number_format($export->successful_rows)
            . ' ' . str('row')->plural($export->successful_rows)
            . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount)
                . ' ' . str('row')->plural($failedRowsCount)
                . ' failed to export.';
        }

        return $body;
    }
}
