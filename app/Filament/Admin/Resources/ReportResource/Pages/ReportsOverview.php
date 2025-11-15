<?php

namespace App\Filament\Admin\Resources\ReportResource\Pages;

use Filament\Resources\Pages\Page;

class ReportsOverview extends Page
{
    protected static string $resource = \App\Filament\Admin\Resources\ReportResource::class;

    protected static string $view = 'filament.admin.pages.reports-overview';

    protected function getHeaderActions(): array
    {
        return [
            // Actions can be added here if needed
        ];
    }
}