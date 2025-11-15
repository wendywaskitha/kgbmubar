<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use App\Filament\App\Resources\PengajuanKgbResource\RelationManagers\DokumenPengajuanRelationManager;
use App\Filament\App\Resources\PengajuanKgbResource\Widgets\DokumenVerificationStats;
use App\Models\DokumenPengajuan;
use App\Models\PengajuanKgb;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class VerifikasiPengajuanKgb extends Page
{
    protected static string $resource = PengajuanKgbResource::class;

    protected static string $view = 'filament.app.resources.pengajuan-kgb-resource.pages.verifikasi-pengajuan-kgb';

    protected static ?string $title = 'Verifikasi Dokumen KGB';

    public PengajuanKgb $record;

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
    }

    protected function resolveRecord(int | string $key): PengajuanKgb
    {
        $record = static::getResource()::getEloquentQuery()->find($key);

        if (!$record) {
            abort(404);
        }

        return $record;
    }

    public function getHeaderActions(): array
    {
        $dokumens = DokumenPengajuan::where('pengajuan_kgb_id', $this->record->id)->get();
        $allValid = $dokumens->count() > 0 && $dokumens->every(fn($d) => $d->status_verifikasi === 'valid');
        $anyInvalid = $dokumens->contains(fn($d) => in_array($d->status_verifikasi, ['tidak_valid', 'revisi']));

        return [
            \Filament\Actions\Action::make('ajukan_ke_kabupaten')
                ->label('Ajukan ke Kabupaten')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->action(function () {
                    $this->record->update([
                        'status' => 'verifikasi_dinas',
                        'tanggal_verifikasi_dinas' => now(),
                    ]);

                    Notification::make()
                        ->title('Berhasil!')
                        ->body('Pengajuan KGB telah diajukan ke Kabupaten.')
                        ->success()
                        ->send();

                    return redirect(PengajuanKgbResource::getUrl('index'));
                })
                ->visible(fn() => Auth::user()->role === 'verifikator_dinas' || Auth::user()->role === 'admin_dinas')
                ->disabled(!$allValid)
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pengajuan ke Kabupaten')
                ->modalDescription('Pastikan semua dokumen telah diverifikasi dengan benar sebelum mengajukan ke Kabupaten.')
                ->modalSubmitActionLabel('Ajukan'),

            \Filament\Actions\Action::make('kembalikan_ke_pegawai')
                ->label('Kembalikan ke Pegawai')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->action(function () {
                    $this->record->update([
                        'status' => 'diajukan', // Change back to initial submitted state
                        'jumlah_revisi' => $this->record->jumlah_revisi + 1,
                    ]);

                    Notification::make()
                        ->title('Berhasil!')
                        ->body('Pengajuan KGB telah dikembalikan ke pegawai untuk diperbaiki.')
                        ->warning()
                        ->send();

                    return redirect(PengajuanKgbResource::getUrl('index'));
                })
                ->visible(fn() => Auth::user()->role === 'verifikator_dinas' || Auth::user()->role === 'admin_dinas')
                ->disabled(!$anyInvalid)
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pengembalian ke Pegawai')
                ->modalDescription('Pengajuan ini akan dikembalikan ke pegawai untuk perbaikan.')
                ->modalSubmitActionLabel('Kembalikan'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DokumenVerificationStats::class => [
                'pengajuanId' => $this->record->id,
            ],
        ];
    }
}
