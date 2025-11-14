<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PengajuanKgb;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PengajuanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengajuan', PengajuanKgb::count())
                ->description('Jumlah seluruh pengajuan')
                ->descriptionIcon('heroicon-m-document-text'),
            
            Stat::make('Pengajuan Baru', PengajuanKgb::where('status', 'diajukan')->count())
                ->description('Pengajuan menunggu verifikasi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            
            Stat::make('Pengajuan Disetujui', PengajuanKgb::where('status', 'disetujui')->count())
                ->description('Pengajuan yang disetujui')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}