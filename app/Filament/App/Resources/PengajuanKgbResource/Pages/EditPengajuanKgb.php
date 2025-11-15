<?php

namespace App\Filament\App\Resources\PengajuanKgbResource\Pages;

use App\Filament\App\Resources\PengajuanKgbResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPengajuanKgb extends EditRecord
{
    protected static string $resource = PengajuanKgbResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn ($record) => $record->status === 'draft')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Pengajuan Dihapus')
                        ->body('Pengajuan KGB berhasil dihapus.')
                ),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Pastikan pegawai_id dan user_pengaju_id tidak berubah
        $data['pegawai_id'] = $this->record->pegawai_id;
        $data['user_pengaju_id'] = $this->record->user_pengaju_id;
        
        // Set tanggal_pengajuan jika status berubah ke diajukan
        if (isset($data['status']) && $data['status'] === 'diajukan' && $this->record->status === 'draft') {
            $data['tanggal_pengajuan'] = now();
        }
        
        return $data;
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Pengajuan Berhasil Diperbarui')
            ->body('Perubahan pengajuan KGB Anda telah disimpan.');
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function beforeFill(): void
    {
        // Cek apakah pengajuan bisa diedit
        if (!in_array($this->record->status, ['draft', 'ditolak'])) {
            Notification::make()
                ->warning()
                ->title('Tidak Dapat Mengedit')
                ->body('Pengajuan yang sudah diajukan tidak dapat diedit. Silakan hubungi administrator jika perlu perubahan.')
                ->persistent()
                ->send();
            
            $this->redirect($this->getResource()::getUrl('view', ['record' => $this->record]));
        }
    }
}
