<?php

namespace App\Filament\Admin\Resources\SkTemplateResource\Pages;

use App\Filament\Admin\Resources\SkTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSkTemplates extends ListRecords
{
    protected static string $resource = SkTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
