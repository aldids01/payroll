<?php

namespace App\Filament\Resources\PensionResource\Pages;

use App\Filament\Resources\PensionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPension extends EditRecord
{
    protected static string $resource = PensionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
