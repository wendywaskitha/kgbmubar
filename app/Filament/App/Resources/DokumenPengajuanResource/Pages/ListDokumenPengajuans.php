<?php

namespace App\Filament\App\Resources\DokumenPengajuanResource\Pages;

use App\Filament\App\Resources\DokumenPengajuanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDokumenPengajuans extends ListRecords
{
    protected static string $resource = DokumenPengajuanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}