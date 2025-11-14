<?php

namespace App\Filament\Pegawai\Widgets;

use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProfileInfo extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;

        if (!$pegawai) {
            return [
                Stat::make('Profil Pegawai', 'Belum Terdaftar')
                    ->description('Anda belum terdaftar sebagai pegawai')
                    ->descriptionIcon('heroicon-m-user')
                    ->color('warning'),
            ];
        }

        return [
            Stat::make('Nama', $pegawai->name)
                ->description('Nama lengkap pegawai')
                ->descriptionIcon('heroicon-m-user'),

            Stat::make('NIP', $pegawai->nip)
                ->description('Nomor Induk Pegawai')
                ->descriptionIcon('heroicon-m-identification'),

            Stat::make('Jabatan', $pegawai->jabatan)
                ->description('Jabatan terkini')
                ->descriptionIcon('heroicon-m-briefcase'),
        ];
    }
}
