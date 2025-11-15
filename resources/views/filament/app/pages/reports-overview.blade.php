<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Laporan & Analytics Dinas</h1>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Pegawai Dinas -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <x-heroicon-o-users class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Pegawai Dinas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Pegawai::where('tenant_id', auth()->user()->tenant_id)->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Eligible untuk KGB -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <x-heroicon-o-user-plus class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Eligible untuk KGB</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Services\ReminderService::class ? (new \App\Services\ReminderService())->getEligiblePegawaiForTenant(auth()->user()->tenant_id)->count() : 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Pengajuan Aktif -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-indigo-100 text-indigo-600 mr-4">
                    <x-heroicon-o-document-duplicate class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pengajuan Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->whereIn('status', ['draft', 'diajukan', 'verifikasi_dinas', 'verifikasi_kabupaten'])->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Verifikasi Dinas -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                    <x-heroicon-o-exclamation-circle class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Verifikasi Dinas</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->where('status', 'verifikasi_dinas')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Disetujui Bulan Ini -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                    <x-heroicon-o-check-badge class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Disetujui Bulan Ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->where('status', 'disetujui')->whereMonth('tanggal_approve', now()->month)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Pengajuan per Bulan - Bar Chart -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pengajuan per Bulan (Tahun Ini)</h2>
            <div id="pengajuanPerBulanChart" class="h-80"></div>
        </div>

        <!-- Status Pengajuan - Donut Chart -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Pengajuan</h2>
            <div id="statusPengajuanChart" class="h-80"></div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <a href="{{ route('filament.app.resources.pengajuan-kgb.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg p-4 text-center transition duration-300">
            <x-heroicon-o-plus-circle class="w-8 h-8 mx-auto mb-2" />
            <span class="font-medium">Buat Pengajuan Baru</span>
        </a>
        <a href="#" onclick="sendReminders()" class="bg-amber-500 hover:bg-amber-600 text-white rounded-lg p-4 text-center transition duration-300">
            <x-heroicon-o-bell-alert class="w-8 h-8 mx-auto mb-2" />
            <span class="font-medium">Kirim Reminder</span>
        </a>
        <a href="{{ route('filament.app.resources.pegawai.index') }}" class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 text-center transition duration-300">
            <x-heroicon-o-user-group class="w-8 h-8 mx-auto mb-2" />
            <span class="font-medium">Data Pegawai</span>
        </a>
        <a href="{{ route('filament.app.resources.pengajuan-kgb.index') }}" class="bg-purple-600 hover:bg-purple-700 text-white rounded-lg p-4 text-center transition duration-300">
            <x-heroicon-o-document-chart-bar class="w-8 h-8 mx-auto mb-2" />
            <span class="font-medium">Lihat Semua Pengajuan</span>
        </a>
    </div>

    <!-- Recent Submissions Table -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pengajuan Terbaru</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Ajukan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php $counter = 1; @endphp
                    @foreach(\App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->with('pegawai')->latest()->limit(15)->get() as $pengajuan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $counter++ }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pengajuan->pegawai->nip ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->pegawai->nama ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->tanggal_pengajuan ? $pengajuan->tanggal_pengajuan->format('d M Y') : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($pengajuan->status === 'draft') bg-gray-100 text-gray-800 
                                @elseif($pengajuan->status === 'diajukan') bg-blue-100 text-blue-800 
                                @elseif($pengajuan->status === 'verifikasi_dinas') bg-yellow-100 text-yellow-800 
                                @elseif($pengajuan->status === 'verifikasi_kabupaten') bg-orange-100 text-orange-800 
                                @elseif($pengajuan->status === 'disetujui') bg-green-100 text-green-800 
                                @elseif($pengajuan->status === 'ditolak') bg-red-100 text-red-800 
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $pengajuan->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('filament.app.resources.pengajuan-kgb.edit', ['record' => $pengajuan->id]) }}" class="text-blue-600 hover:text-blue-900">Lihat</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pegawai Eligible KGB Widget -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Pegawai Eligible KGB</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TMT KGB Terakhir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eligible Since</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $reminderService = new \App\Services\ReminderService();
                        $eligiblePegawai = $reminderService->getEligiblePegawaiForTenant(auth()->user()->tenant_id);
                    @endphp
                    @foreach($eligiblePegawai as $pegawai)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pegawai->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pegawai->nip }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pegawai->tmt_kgb_terakhir->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pegawai->tmt_kgb_terakhir->addYears(2)->format('d M Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('filament.app.resources.pengajuan-kgb.create') }}?pegawai_id={{ $pegawai->id }}" class="text-blue-600 hover:text-blue-900">Ajukan KGB</a>
                        </td>
                    </tr>
                    @endforeach
                    @if($eligiblePegawai->isEmpty())
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada pegawai yang eligible untuk KGB saat ini.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart initialization code will go here
        document.addEventListener('DOMContentLoaded', function() {
            // Pengajuan per Bulan - Bar Chart
            const pengajuanPerBulanCtx = document.getElementById('pengajuanPerBulanChart').getContext('2d');
            new Chart(pengajuanPerBulanCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @php
                            $months = [];
                            for ($i = 0; $i < 12; $i++) {
                                $months[] = now()->startOfYear()->addMonths($i)->format('M');
                            }
                            echo '"' . implode('","', $months) . '"';
                        @endphp
                    ],
                    datasets: [{
                        label: 'Pengajuan Bulan Ini vs Tahun Lalu',
                        data: [
                            @php
                                $currentYearData = [];
                                for ($i = 0; $i < 12; $i++) {
                                    $month = $i + 1;
                                    $currentYearData[] = \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)
                                        ->whereMonth('created_at', $month)
                                        ->whereYear('created_at', now()->year)
                                        ->count();
                                }
                                echo implode(',', $currentYearData);
                            @endphp
                        ],
                        backgroundColor: 'rgba(99, 102, 241, 0.7)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Status Pengajuan - Doughnut Chart
            const statusPengajuanCtx = document.getElementById('statusPengajuanChart').getContext('2d');
            new Chart(statusPengajuanCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Draft', 'Diajukan', 'Verifikasi Dinas', 'Verifikasi Kabupaten', 'Disetujui', 'Ditolak'],
                    datasets: [{
                        data: [
                            {{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->where('status', 'draft')->count() }},
                            {{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->where('status', 'diajukan')->count() }},
                            {{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->where('status', 'verifikasi_dinas')->count() }},
                            {{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->where('status', 'verifikasi_kabupaten')->count() }},
                            {{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->where('status', 'disetujui')->count() }},
                            {{ \App\Models\PengajuanKgb::where('tenant_id', auth()->user()->tenant_id)->whereIn('status', ['ditolak', 'ditolak_dinas', 'ditolak_kabupaten'])->count() }}
                        ],
                        backgroundColor: [
                            '#9CA3AF', // gray for draft
                            '#3B82F6', // blue for diajukan
                            '#F59E0B', // yellow for verifikasi_dinas
                            '#F97316', // orange for verifikasi_kabupaten
                            '#10B981', // green for disetujui
                            '#EF4444'  // red for ditolak
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });

        function sendReminders() {
            // This function would trigger the reminder service for this tenant
            // In a real implementation, this would make an API call
            alert('Fungsi kirim reminder akan diimplementasikan. Saat ini reminder dikirim otomatis harian.');
        }
    </script>
</div>