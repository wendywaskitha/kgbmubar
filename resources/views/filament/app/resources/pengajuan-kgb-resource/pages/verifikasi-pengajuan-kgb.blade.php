<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Widget Statistik --}}
        @if($this->getHeaderWidgets())
            <x-filament::section>
                <x-slot name="heading">
                    Statistik Verifikasi Dokumen
                </x-slot>
                <div class="mt-4">
                    <x-filament-widgets::widgets
                        :widgets="$this->getHeaderWidgets()"
                        :columns="[
                            'default' => 2,
                        ]"
                        :data="[
                            'pengajuanId' => $this->record->id,
                        ]"
                    />
                </div>
            </x-filament::section>
        @endif

        {{-- Informasi Pegawai --}}
        <x-filament::section>
            <x-slot name="heading">
                Informasi Pengajuan dan Pegawai
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Pegawai</p>
                    <p class="text-gray-900">{{ $this->record->pegawai->name ?? 'Tidak ditemukan' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">NIP</p>
                    <p class="text-gray-900">{{ $this->record->pegawai->nip ?? 'Tidak ditemukan' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Status Pengajuan</p>
                    <p class="text-gray-900">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($this->record->status === 'diajukan') bg-yellow-100 text-yellow-800
                            @elseif($this->record->status === 'verifikasi_dinas') bg-blue-100 text-blue-800
                            @elseif($this->record->status === 'disetujui') bg-green-100 text-green-800
                            @elseif($this->record->status === 'ditolak') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $this->record->status)) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">TMT KGB Baru</p>
                    <p class="text-gray-900">{{ $this->record->tmt_kgb_baru?->format('d M Y') ?? '-' }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Dokumen Verifikasi - Using Filament v3 Relation Manager --}}
        <x-filament::section>
            <x-slot name="heading">
                Daftar Dokumen Persyaratan
            </x-slot>
            <div class="mt-4">
                @foreach($this->getRelationManagers() as $relationManager)
                    @livewire($relationManager, [
                        'ownerRecord' => $this->getRecord(),
                        'pageClass' => static::class,
                    ], key($relationManager))
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
