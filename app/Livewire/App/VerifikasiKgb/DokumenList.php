<?php

namespace App\Livewire\App\VerifikasiKgb;

use App\Models\DokumenPengajuan;
use App\Models\PengajuanKgb;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

        // Cek apakah file ada di storage
        $fileExists = false;
        $fileUrl = null;
        
        // Coba berbagai kemungkinan path
        if (Storage::disk('public')->exists($dokumen->path_file)) {
            $fileExists = true;
            $fileUrl = Storage::disk('public')->url($dokumen->path_file);
        } elseif (Storage::exists($dokumen->path_file)) {
            $fileExists = true;
            $fileUrl = Storage::url($dokumen->path_file);
        } elseif (file_exists(public_path($dokumen->path_file))) {
            $fileExists = true;
            $fileUrl = asset($dokumen->path_file);
        }
        
        if ($fileExists && $fileUrl) {
            $this->selectedDokumen = $dokumen;
            
            // Deteksi tipe file
            $extension = strtolower(pathinfo($dokumen->path_file, PATHINFO_EXTENSION));
            $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
            
            $this->dispatch('open-document-modal', [
                'url' => $fileUrl,
                'isImage' => $isImage,
                'fileName' => $dokumen->nama_file
            ]);
        } else {
            Notification::make()
                ->title('File tidak ditemukan')
                ->body('Path: ' . $dokumen->path_file)
                ->danger()
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
