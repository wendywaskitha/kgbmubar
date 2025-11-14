<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\PengajuanKgb;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PengajuanStatus extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;
        
        if (!$pegawai) {
            return [
                Stat::make('Status Pengajuan', 'Belum Terdaftar')
                    ->description('Anda belum terdaftar sebagai pegawai')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }
        
        return [
            Stat::make('Total Pengajuan', PengajuanKgb::where('pegawai_id', $pegawai->id)->count())
                ->description('Jumlah pengajuan Anda')
                ->descriptionIcon('heroicon-m-document-text'),
            
            Stat::make('Pengajuan Aktif', PengajuanKgb::where('pegawai_id', $pegawai->id)->where('status', 'diajukan')->count())
                ->description('Pengajuan menunggu verifikasi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),
            
            Stat::make('Pengajuan Selesai', PengajuanKgb::where('pegawai_id', $pegawai->id)->whereIn('status', ['disetujui', 'ditolak'])->count())
                ->description('Pengajuan selesai diproses')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}