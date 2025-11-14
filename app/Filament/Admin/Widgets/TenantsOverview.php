<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Tenant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TenantsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Dinas/OPD', Tenant::count())
                ->description('Jumlah dinas terdaftar')
                ->descriptionIcon('heroicon-m-building-office'),
            
            Stat::make('Dinas Aktif', Tenant::where('is_active', true)->count())
                ->description('Dinas yang aktif')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Dinas Tidak Aktif', Tenant::where('is_active', false)->count())
                ->description('Dinas yang tidak aktif')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}