<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Widget Statistik --}}
        <x-filament::section>
            <x-slot name="heading">
                Statistik Verifikasi Dokumen
            </x-slot>
            <div class="mt-4">
                @if($this->getCachedHeaderWidgets())
                    <x-filament-widgets::widgets
                        :widgets="$this->getCachedHeaderWidgets()"
                        :columns="2"
                    />
                @endif
            </div>
        </x-filament::section>

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

        {{-- Dokumen Verifikasi --}}
        <div class="flex flex-col gap-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold">Daftar Dokumen Persyaratan</h3>
            </div>

            {{ \Filament\Support\Facades\FilamentView::renderHook(
                'panels::resource.pages.list-records.table.before',
                scopes: [static::getResource()::getTableModelClass()]
            ) }}

            <div class="px-4 py-4 bg-white rounded-xl border border-gray-200 shadow-sm">
                @livewire(\Filament\Tables\Http\Livewire\RelationManager::class, [
                    'ownerRecord' => $this->record,
                    'relationManager' => \App\Filament\App\Resources\PengajuanKgbResource\RelationManagers\DokumenPengajuanRelationManager::class,
                    'pageClass' => static::class,
                ], key('dokumen-relation-manager'))
            </div>
        </div>
    </div>
</x-filament-panels::page>
