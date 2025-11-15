<?php

namespace App\Livewire\App\VerifikasiKgb;

use App\Models\DokumenPengajuan;
use App\Models\PengajuanKgb;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DokumenList extends Component
{
    public $pengajuanId;
    public $dokumens;
    public $selectedDokumen = null;

    public function mount($pengajuanId)
    {
        $this->pengajuanId = $pengajuanId;
        $this->loadDokumens();
    }

    public function loadDokumens()
    {
        $this->dokumens = DokumenPengajuan::where('pengajuan_kgb_id', $this->pengajuanId)
            ->orderBy('jenis_dokumen')
            ->get();
    }

    public function viewDokumen($dokumenId)
    {
        $dokumen = DokumenPengajuan::find($dokumenId);
        
        if (!$dokumen) {
            Notification::make()
                ->title('Dokumen tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $fileUrl = null;
        $pathToCheck = $dokumen->path_file;
        
        // Log untuk debugging
        Log::info('Checking file path: ' . $pathToCheck);
        
        // Method 1: Check public disk dengan path langsung
        if (Storage::disk('public')->exists($pathToCheck)) {
            $fileUrl = Storage::disk('public')->url($pathToCheck);
            Log::info('File found in public disk: ' . $fileUrl);
        }
        // Method 2: Coba tanpa 'public/' prefix jika ada
        elseif (str_starts_with($pathToCheck, 'public/')) {
            $pathWithoutPublic = str_replace('public/', '', $pathToCheck);
            if (Storage::disk('public')->exists($pathWithoutPublic)) {
                $fileUrl = Storage::disk('public')->url($pathWithoutPublic);
                Log::info('File found without public prefix: ' . $fileUrl);
            }
        }
        // Method 3: Check default storage
        elseif (Storage::exists($pathToCheck)) {
            $fileUrl = Storage::url($pathToCheck);
            Log::info('File found in default storage: ' . $fileUrl);
        }
        // Method 4: Check langsung di public path
        elseif (file_exists(public_path($pathToCheck))) {
            $fileUrl = asset($pathToCheck);
            Log::info('File found in public path: ' . $fileUrl);
        }
        // Method 5: Check storage/app/public path
        elseif (file_exists(storage_path('app/public/' . $pathToCheck))) {
            $fileUrl = asset('storage/' . $pathToCheck);
            Log::info('File found in storage/app/public: ' . $fileUrl);
        }
        
        if ($fileUrl) {
            $this->selectedDokumen = $dokumen;
            
            // Deteksi tipe file
            $extension = strtolower(pathinfo($dokumen->path_file, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp']);
            
            $this->dispatch('open-document-modal', [
                'url' => $fileUrl,
                'isImage' => $isImage,
                'fileName' => $dokumen->nama_file,
                'fileType' => $extension
            ]);
            
            Log::info('Dispatched modal event', [
                'url' => $fileUrl,
                'isImage' => $isImage,
                'fileName' => $dokumen->nama_file
            ]);
        } else {
            Log::error('File not found in any location: ' . $pathToCheck);
            
            Notification::make()
                ->title('File tidak ditemukan')
                ->body('Path: ' . $pathToCheck . ' - Pastikan file sudah diupload dan storage link sudah dibuat (php artisan storage:link)')
                ->danger()
                ->duration(10000)
                ->send();
        }
    }

    public function verifikasiDokumen($dokumenId)
    {
        $dokumen = DokumenPengajuan::find($dokumenId);
        
        if ($dokumen) {
            $newStatus = $dokumen->status_verifikasi === 'valid' ? 'belum_diperiksa' : 'valid';
            
            $dokumen->update([
                'status_verifikasi' => $newStatus,
                'verifikator_id' => Auth::id(),
                'tanggal_verifikasi' => $newStatus === 'valid' ? now() : null,
            ]);

            $this->loadDokumens();

            Notification::make()
                ->title('Dokumen ' . ($newStatus === 'valid' ? 'diverifikasi' : 'dibatalkan'))
                ->success()
                ->send();
        }
    }

    public function setTidakValid($dokumenId, $catatan = null)
    {
        $dokumen = DokumenPengajuan::find($dokumenId);
        
        if ($dokumen) {
            $dokumen->update([
                'status_verifikasi' => 'tidak_valid',
                'catatan_verifikasi' => $catatan,
                'verifikator_id' => Auth::id(),
                'tanggal_verifikasi' => now(),
            ]);

            $this->loadDokumens();

            Notification::make()
                ->title('Dokumen ditandai tidak valid')
                ->warning()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.app.verifikasi-kgb.dokumen-list');
    }
}
