<?php

namespace App\Filament\Admin\Resources\SkKgbResource\Pages;

use App\Filament\Admin\Resources\SkKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSkKgbs extends ListRecords
{
    protected static string $resource = SkKgbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}