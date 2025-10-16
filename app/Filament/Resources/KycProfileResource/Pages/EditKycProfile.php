<?php

namespace App\Filament\Resources\KycProfileResource\Pages;

use App\Filament\Resources\KycProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKycProfile extends EditRecord
{
    protected static string $resource = KycProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
