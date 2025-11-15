<div>
    <div class="space-y-4">
        @forelse($dokumens as $dokumen)
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-gray-300 dark:hover:border-gray-600 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ ucwords(str_replace('_', ' ', $dokumen->jenis_dokumen)) }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ $dokumen->nama_file }}
                                </p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $dokumen->tipe_file }} 
                                    </span>
                                    @if($dokumen->tanggal_upload)
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            • Diupload {{ $dokumen->tanggal_upload->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 ml-4">
                        {{-- Status Badge --}}
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                            @if($dokumen->status_verifikasi === 'valid') bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20
                            @elseif($dokumen->status_verifikasi === 'tidak_valid') bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-500/10 dark:text-red-400 dark:ring-red-500/20
                            @elseif($dokumen->status_verifikasi === 'revisi') bg-yellow-50 text-yellow-700 ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20
                            @else bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $dokumen->status_verifikasi)) }}
                        </span>

                        {{-- Button View --}}
                        <button
                            wire:click="viewDokumen({{ $dokumen->id }})"
                            type="button"
                            class="inline-flex items-center gap-x-1.5 rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700"
                            title="Lihat Dokumen">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Lihat
                        </button>

                        {{-- Button Verifikasi Toggle --}}
                        <button
                            wire:click="verifikasiDokumen({{ $dokumen->id }})"
                            type="button"
                            class="inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold shadow-sm
                                @if($dokumen->status_verifikasi === 'valid')
                                    bg-green-600 text-white hover:bg-green-500
                                @else
                                    bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600
                                @endif"
                            title="{{ $dokumen->status_verifikasi === 'valid' ? 'Batalkan Verifikasi' : 'Verifikasi Dokumen' }}">
                            @if($dokumen->status_verifikasi === 'valid')
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Terverifikasi
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Verifikasi
                            @endif
                        </button>
                    </div>
                </div>

                {{-- Catatan Verifikasi --}}
                @if($dokumen->catatan_verifikasi)
                    <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300">Catatan Verifikasi:</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $dokumen->catatan_verifikasi }}</p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada dokumen</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Belum ada dokumen yang diupload untuk pengajuan ini.</p>
            </div>
        @endforelse
    </div>

    {{-- Summary Status --}}
    @if($dokumens->count() > 0)
        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span class="font-medium">{{ $dokumens->where('status_verifikasi', 'valid')->count() }}</span>
                    dari 
                    <span class="font-medium">{{ $dokumens->count() }}</span>
                    dokumen terverifikasi
                </div>

                @if($dokumens->where('status_verifikasi', 'valid')->count() === $dokumens->count())
                    <div class="flex items-center gap-2 text-sm font-medium text-green-700 dark:text-green-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Semua dokumen sudah terverifikasi
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Modal untuk View PDF --}}
    <div 
        x-data="{ open: false, documentUrl: '' }"
        @open-document-modal.window="open = true; documentUrl = $event.detail.url"
        x-show="open"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true">
        
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div 
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                @click="open = false"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div 
                x-show="open"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-5xl sm:w-full sm:p-6">
                
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Preview Dokumen</h3>
                    <button 
                        @click="open = false"
                        type="button"
                        class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="w-full h-[600px]">
                    <iframe 
                        :src="documentUrl" 
                        class="w-full h-full border border-gray-300 dark:border-gray-600 rounded"
                        frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
