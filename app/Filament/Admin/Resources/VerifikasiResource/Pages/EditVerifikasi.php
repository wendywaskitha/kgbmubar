<?php

namespace App\Filament\Admin\Resources\VerifikasiResource\Pages;

use App\Filament\Admin\Resources\VerifikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVerifikasi extends EditRecord
{
    protected static string $resource = VerifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}