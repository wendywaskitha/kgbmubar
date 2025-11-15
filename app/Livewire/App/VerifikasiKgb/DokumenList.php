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
        
        Log::info('Checking file path: ' . $pathToCheck);
        
        // Method 1: Path dengan dokumen-pengajuan
        if (Storage::disk('public')->exists('dokumen-pengajuan/' . $pathToCheck)) {
            $fileUrl = asset('storage/dokumen-pengajuan/' . $pathToCheck);
            Log::info('File found with dokumen-pengajuan prefix: ' . $fileUrl);
        }
        // Method 2: Path sudah include dokumen-pengajuan
        elseif (str_contains($pathToCheck, 'dokumen-pengajuan')) {
            $cleanPath = str_replace('dokumen-pengajuan/', '', $pathToCheck);
            if (Storage::disk('public')->exists('dokumen-pengajuan/' . $cleanPath)) {
                $fileUrl = asset('storage/dokumen-pengajuan/' . $cleanPath);
                Log::info('File found after cleaning path: ' . $fileUrl);
            }
        }
        // Method 3: Direct path check
        elseif (Storage::disk('public')->exists($pathToCheck)) {
            $fileUrl = Storage::disk('public')->url($pathToCheck);
            Log::info('File found in public disk: ' . $fileUrl);
        }
        // Method 4: Check dengan berbagai prefix
        else {
            $prefixes = ['dokumen-pengajuan/', 'storage/dokumen-pengajuan/', ''];
            foreach ($prefixes as $prefix) {
                $testPath = $prefix . ltrim($pathToCheck, '/');
                
                if (Storage::disk('public')->exists($testPath)) {
                    $fileUrl = asset('storage/' . $testPath);
                    Log::info('File found with prefix: ' . $prefix . ' -> ' . $fileUrl);
                    break;
                }
                
                if (file_exists(public_path('storage/' . $testPath))) {
                    $fileUrl = asset('storage/' . $testPath);
                    Log::info('File found in filesystem: ' . $fileUrl);
                    break;
                }
            }
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
            Log::error('Checked paths: ', [
                'public_disk' => Storage::disk('public')->path($pathToCheck),
                'dokumen-pengajuan' => Storage::disk('public')->path('dokumen-pengajuan/' . $pathToCheck),
                'public_path' => public_path('storage/' . $pathToCheck)
            ]);
            
            Notification::make()
                ->title('File tidak ditemukan')
                ->body('Path: ' . $pathToCheck . ' | Lokasi: public/storage/dokumen-pengajuan/')
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
