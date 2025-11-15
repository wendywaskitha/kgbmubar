<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Informasi Pegawai --}}
        <x-filament::section>
            <x-slot name="heading">
                Identitas Pegawai
            </x-slot>
            @livewire('app.verifikasi-kgb.pegawai-info', ['pengajuanId' => $this->record->id], key('pegawai-info-' . $this->record->id))
        </x-filament::section>

        {{-- List Dokumen dengan Verifikasi --}}
        <x-filament::section>
            <x-slot name="heading">
                Daftar Dokumen Persyaratan
            </x-slot>
            <x-slot name="description">
                Verifikasi setiap dokumen dengan melihat file dan menandai sebagai valid.
            </x-slot>
            @livewire('app.verifikasi-kgb.dokumen-list', ['pengajuanId' => $this->record->id], key('dokumen-list-' . $this->record->id))
        </x-filament::section>
        <x-filament::section>
            <x-slot name="heading">
                Finalisasi
            </x-slot>
            @livewire('app.verifikasi-kgb.dokumen-list', ['pengajuanId' => $this->record->id], key('final-dokumen-list-' . $this->record->id))
            @php
                $dokumens = \App\Models\DokumenPengajuan::where('pengajuan_kgb_id', $this->record->id)->get();
                $allValid = $dokumens->count() > 0 && $dokumens->every(fn($d) => $d->status_verifikasi === 'valid');
            @endphp
            <button
                type="button"
                class="px-6 py-3 rounded-lg font-semibold text-white shadow bg-primary-600 hover:bg-primary-700 disabled:bg-gray-200 disabled:text-gray-400 mt-4"
                @if(! $allValid) disabled @endif
            >
                Ajukan ke Kabupaten
            </button>
            @if(! $allValid)
                <div class="mt-2 text-sm text-red-600">Semua dokumen harus diverifikasi valid.</div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
