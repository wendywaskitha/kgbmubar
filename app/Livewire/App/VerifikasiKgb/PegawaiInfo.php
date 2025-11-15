<?php

namespace App\Livewire\App\VerifikasiKgb;

use App\Models\PengajuanKgb;
use Livewire\Component;

class PegawaiInfo extends Component
{
    public $pengajuanId;
    public $pengajuan;
    public $pegawai;

    public function mount($pengajuanId)
    {
        $this->pengajuanId = $pengajuanId;
        $this->loadData();
    }

    public function loadData()
    {
        $this->pengajuan = PengajuanKgb::with('pegawai')->find($this->pengajuanId);
        $this->pegawai = $this->pengajuan->pegawai;
    }

    public function render()
    {
        return view('livewire.app.verifikasi-kgb.pegawai-info');
    }
}
