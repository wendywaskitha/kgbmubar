<?php

namespace App\Filament\Pegawai\Resources\DokumenPengajuanResource\Pages;

use App\Filament\Pegawai\Resources\DokumenPengajuanResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CreateDokumenPengajuan extends CreateRecord
{
    protected static string $resource = DokumenPengajuanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika ada file, otomatis set ukuran dan tipe
        if (!empty($data['path_file'])) {
            $relative = ltrim($data['path_file'], '/');
            $disk = 'public';
            if (Storage::disk($disk)->exists($relative)) {
                $data['ukuran_file'] = Storage::disk($disk)->size($relative);
                $data['tipe_file'] = pathinfo($relative, PATHINFO_EXTENSION) ?: 'pdf';
            } else {
                $data['ukuran_file'] = 0;
                $data['tipe_file'] = '';
            }
        } else {
            $data['ukuran_file'] = 0;
            $data['tipe_file'] = '';
        }
        // Nama file otomatis dari path
        if (!empty($data['path_file'])) {
            $data['nama_file'] = basename($data['path_file']);
        }
        return $data;
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Dokumen berhasil diunggah')
            ->body('Dokumen pengajuan berhasil disimpan, ukuran dan tipe file otomatis terisi.');
    }
}
