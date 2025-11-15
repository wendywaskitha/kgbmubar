<?php

namespace App\Filament\Admin\Resources\SkTemplateResource\Pages;

use App\Filament\Admin\Resources\SkTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSkTemplate extends EditRecord
{
    protected static string $resource = SkTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
