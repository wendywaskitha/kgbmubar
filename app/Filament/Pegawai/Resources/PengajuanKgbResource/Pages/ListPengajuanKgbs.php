<?php

namespace App\Filament\Pegawai\Resources\PengajuanKgbResource\Pages;

use App\Filament\Pegawai\Resources\PengajuanKgbResource;
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