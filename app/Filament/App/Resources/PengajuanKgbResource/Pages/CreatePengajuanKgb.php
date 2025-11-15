<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePengajuanKgb extends CreateRecord
{
    protected static string $resource = PengajuanKgbResource::class;
    
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $pegawai = $user->pegawai;
        
        // Validasi: User harus punya data pegawai
        if (!$pegawai) {
            Notification::make()
                ->danger()
                ->title('Tidak Dapat Membuat Pengajuan')
                ->body('Akun Anda belum terhubung dengan data pegawai. Silakan hubungi administrator.')
                ->persistent()
                ->send();
            
            $this->halt();
        }
        
        // Auto-fill pegawai_id dan user_pengaju_id
        $data['pegawai_id'] = $pegawai->id;
        $data['user_pengaju_id'] = $user->id;
        
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
}
