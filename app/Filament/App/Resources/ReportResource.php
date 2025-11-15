<?php

namespace App\Filament\App\Resources;

use App\Filament\App\Resources\ReportResource\Pages\ReportsOverview;
use Filament\Resources\Resource;

class ReportResource extends Resource
{
    protected static ?string $model = null; // No model needed for reports

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Laporan & Analytics';

    protected static ?string $pluralModelLabel = 'Laporan';

    public static function canAccess(): bool
    {
        // Only allow access to users with app panel roles
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        return $user->hasRole(['admin_dinas', 'verifikator_dinas', 'operator_dinas']) 
               && $user->tenant_id !== null;
    }

    public static function getPages(): array
    {
        return [
            'index' => ReportsOverview::route('/'),
        ];
    }
}