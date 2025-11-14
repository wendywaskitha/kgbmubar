<?php

namespace App\Filament\App\Widgets;

use App\Models\Pegawai;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PegawaiOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        
        return [
            Stat::make('Total Pegawai', Pegawai::where('tenant_id', $tenantId)->count())
                ->description('Jumlah pegawai di dinas Anda')
                ->descriptionIcon('heroicon-m-user-group'),
            
            Stat::make('Pegawai Aktif', Pegawai::where('tenant_id', $tenantId)->where('is_active', true)->count())
                ->description('Pegawai yang aktif')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            
            Stat::make('Pegawai Tidak Aktif', Pegawai::where('tenant_id', $tenantId)->where('is_active', false)->count())
                ->description('Pegawai yang tidak aktif')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}