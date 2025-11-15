<div>
    @if($pegawai)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">NIP</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pegawai->nip }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nama Pegawai</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pegawai->name }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pangkat/Golongan</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pegawai->pangkat_golongan }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jabatan</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pegawai->jabatan }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unit Kerja</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $pegawai->unit_kerja }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">TMT KGB Terakhir</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                    {{ $pegawai->tmt_kgb_terakhir ? $pegawai->tmt_kgb_terakhir->format('d M Y') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">TMT KGB Baru (Diajukan)</p>
                <p class="mt-1 text-sm font-semibold text-primary-600 dark:text-primary-400">
                    {{ $pengajuan->tmt_kgb_baru ? $pengajuan->tmt_kgb_baru->format('d M Y') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Pengajuan</p>
                <p class="mt-1">
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                        @if($pengajuan->status === 'diajukan') bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/20
                        @elseif($pengajuan->status === 'verifikasi_dinas') bg-blue-50 text-blue-800 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30
                        @elseif($pengajuan->status === 'disetujui') bg-green-50 text-green-800 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20
                        @else bg-gray-50 text-gray-800 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20
                        @endif">
                        {{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                    </span>
                </p>
            </div>
        </div>
    @else
        <p class="text-sm text-gray-500">Data pegawai tidak ditemukan.</p>
    @endif
</div>
