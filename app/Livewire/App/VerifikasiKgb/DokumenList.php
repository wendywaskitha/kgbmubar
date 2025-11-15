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
        
        if ($dokumen && Storage::exists($dokumen->path_file)) {
            $this->selectedDokumen = $dokumen;
            $this->dispatch('open-document-modal', ['url' => Storage::url($dokumen->path_file)]);
        } else {
            Notification::make()
                ->title('File tidak ditemukan')
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
