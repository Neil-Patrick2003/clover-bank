<?php

namespace App\Filament\Resources\ApplicationDocumentResource\Pages;

use App\Filament\Resources\ApplicationDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplicationDocument extends EditRecord
{
    protected static string $resource = ApplicationDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
