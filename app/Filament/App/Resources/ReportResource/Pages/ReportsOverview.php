<?php

namespace App\Filament\App\Resources\ReportResource\Pages;

use Filament\Resources\Pages\Page;

class ReportsOverview extends Page
{
    protected static string $resource = \App\Filament\App\Resources\ReportResource::class;

    protected static string $view = 'filament.app.pages.reports-overview';

    protected function getHeaderActions(): array
    {
        return [
            // Actions can be added here if needed
        ];
    }
}