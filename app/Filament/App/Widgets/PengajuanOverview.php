<?php

namespace App\Filament\App\Widgets;

use App\Models\PengajuanKgb;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PengajuanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        
        return [
            Stat::make('Total Pengajuan', PengajuanKgb::where('tenant_id', $tenantId)->count())
                ->description('Pengajuan di dinas Anda')
                ->descriptionIcon('heroicon-m-document-text'),
            
            Stat::make('Pengajuan Menunggu', PengajuanKgb::where('tenant_id', $tenantId)->whereIn('status', ['diajukan', 'verifikasi_dinas'])->count())
                ->description('Pengajuan menunggu verifikasi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            
            Stat::make('Pengajuan Selesai', PengajuanKgb::where('tenant_id', $tenantId)->whereIn('status', ['disetujui', 'ditolak'])->count())
                ->description('Pengajuan selesai diproses')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}