<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPengajuanKgbs extends ListRecords
{
    protected static string $resource = PengajuanKgbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}