<?php

namespace App\Filament\Pegawai\Resources\PengajuanKgbResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Pegawai\Resources\PengajuanKgbResource;
use Filament\Notifications\Notification;

class CreatePengajuanKgb extends CreateRecord
{
    protected static string $resource = PengajuanKgbResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $pegawai = $user->pegawai;
        if (!$pegawai) {
            Notification::make()
                ->danger()
                ->title('Tidak Dapat Membuat Pengajuan')
                ->body('Akun Anda belum terhubung dengan data pegawai. Silakan hubungi admin.')
                ->persistent()
                ->send();
            $this->halt();
        }
        $data['user_pengaju_id'] = $user->id;
        $data['pegawai_id'] = $pegawai->id;
        // Set jenis_pengajuan = mandiri by default untuk pegawai
        $data['jenis_pengajuan'] = 'mandiri';
        // Set default status = draft (pegawai simpan draft dulu, baru ajukan nanti)
        $data['status'] = 'draft';

        // Set tanggal_pengajuan HANYA jika status diajukan (biasanya tidak terjadi saat create, tapi dari action Ajukan)
        if (isset($data['status']) && $data['status'] === 'diajukan') {
            $data['tanggal_pengajuan'] = now();
        }
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengajuan KGB Berhasil Dibuat')
            ->body('Pengajuan KGB Anda telah berhasil disimpan sebagai draft. Lengkapi dokumen, lalu klik "Ajukan" untuk mengirim ke admin dinas.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // HAPUS method afterCreate() agar notifikasi HANYA dikirim saat action "Ajukan" diklik, BUKAN saat save/create
}
