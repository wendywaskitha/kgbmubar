<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Widgets;

use App\Models\DokumenPengajuan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DokumenVerificationStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected int | string | array $columnSpan = 'full';

    public ?int $pengajuanId = null;

    public function mount(?int $pengajuanId = null): void
    {
        $this->pengajuanId = $pengajuanId;
    }

    protected function getStats(): array
    {
        if (!$this->pengajuanId) {
            return [
                Stat::make('Total Dokumen', 0)
                    ->description('Belum ada data')
                    ->color('gray'),
            ];
        }

        $totalDokumen = DokumenPengajuan::where('pengajuan_kgb_id', $this->pengajuanId)->count();
        $validDokumen = DokumenPengajuan::where('pengajuan_kgb_id', $this->pengajuanId)
            ->where('status_verifikasi', 'valid')
            ->count();
        $belumDiperiksa = DokumenPengajuan::where('pengajuan_kgb_id', $this->pengajuanId)
            ->where('status_verifikasi', 'belum_diperiksa')
            ->count();
        $perluRevisi = DokumenPengajuan::where('pengajuan_kgb_id', $this->pengajuanId)
            ->whereIn('status_verifikasi', ['tidak_valid', 'revisi'])
            ->count();

        $validPercentage = $totalDokumen > 0 ? round(($validDokumen / $totalDokumen) * 100, 1) : 0;

        return [
            Stat::make('Total Dokumen', $totalDokumen),
            Stat::make('Dokumen Valid', $validDokumen)
                ->description($validPercentage . '% dari total')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($validPercentage == 100 ? 'success' : ($validPercentage > 50 ? 'warning' : 'danger')),
            Stat::make('Belum Diperiksa', $belumDiperiksa)
                ->color('gray'),
            Stat::make('Perlu Revisi', $perluRevisi)
                ->color('danger'),
        ];
    }
}