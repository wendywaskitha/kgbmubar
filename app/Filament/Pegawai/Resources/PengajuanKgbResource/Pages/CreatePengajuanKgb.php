<?php

namespace App\Filament\Pegawai\Resources\PengajuanKgbResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Pegawai\Resources\PengajuanKgbResource;
use Filament\Notifications\Notification;
use App\Models\User;

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

        // Set tanggal_pengajuan jika status diajukan
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
            ->body('Pengajuan KGB Anda telah berhasil disimpan.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $user = auth()->user();
        $pegawai = $user->pegawai;

        // Kirim notification ke admin_dinas, verifikator_dinas, operator_dinas di tenant yang sama
        $targetRoles = ['admin_dinas', 'verifikator_dinas', 'operator_dinas'];
        $appRecipients = User::whereIn('role', $targetRoles)
            ->where('tenant_id', $user->tenant_id)
            ->get();

        foreach ($appRecipients as $recipient) {
            Notification::make()
                ->title('Pengajuan KGB Baru')
                ->body('Pengajuan KGB baru diajukan oleh ' . $pegawai->name . ' pada ' . now()->format('d M Y H:i'))
                ->icon('heroicon-o-document-text')
                ->success()
                ->actions([])
                ->sendToDatabase($recipient);
        }
    }
}
