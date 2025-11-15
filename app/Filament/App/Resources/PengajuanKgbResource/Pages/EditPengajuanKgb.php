<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPengajuanKgb extends EditRecord
{
    protected static string $resource = PengajuanKgbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}