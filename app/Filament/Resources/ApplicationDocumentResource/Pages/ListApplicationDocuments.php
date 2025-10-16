<?php

namespace App\Filament\Resources\ApplicationDocumentResource\Pages;

use App\Filament\Resources\ApplicationDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApplicationDocuments extends ListRecords
{
    protected static string $resource = ApplicationDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
