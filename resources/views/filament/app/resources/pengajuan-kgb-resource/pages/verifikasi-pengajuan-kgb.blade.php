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
    </div>
</x-filament-panels::page>
