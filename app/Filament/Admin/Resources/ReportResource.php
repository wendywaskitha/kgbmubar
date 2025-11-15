<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ReportResource\Pages\ReportsOverview;
use Filament\Resources\Resource;

class ReportResource extends Resource
{
    protected static ?string $model = null; // No model needed for reports

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan & Analytics';

    protected static ?string $pluralModelLabel = 'Laporan';

    public static function canAccess(): bool
    {
        // Only allow access to users with admin or verifikator kabupaten roles
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->hasRole(['super_admin_kabupaten', 'verifikator_kabupaten', 'admin_kabupaten']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ReportsOverview::route('/'),
        ];
    }
}