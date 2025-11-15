<?php

namespace App\Filament\Admin\Resources\SkKgbResource\Pages;

use App\Filament\Admin\Resources\SkKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSkKgb extends EditRecord
{
    protected static string $resource = SkKgbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}