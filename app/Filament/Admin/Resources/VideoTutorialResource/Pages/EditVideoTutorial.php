<?php

namespace App\Filament\Admin\Resources\VideoTutorialResource\Pages;

use App\Filament\Admin\Resources\VideoTutorialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVideoTutorial extends EditRecord
{
    protected static string $resource = VideoTutorialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
