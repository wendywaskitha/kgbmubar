<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-6">Laporan & Analytics Kabupaten</h1>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Dinas Aktif -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600 mr-4">
                    <x-heroicon-o-building-office class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Dinas Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\Tenant::count() }}</p>
                </div>
            </div>
            <a href="{{ route('filament.admin.resources.tenants.index') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat detail</a>
        </div>

        <!-- Pengajuan Bulan Ini -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600 mr-4">
                    <x-heroicon-o-document-text class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pengajuan Bulan Ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\PengajuanKgb::whereMonth('created_at', now()->month)->count() }}</p>
                </div>
            </div>
            <a href="{{ route('filament.admin.resources.pengajuan-kgb.index') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat detail</a>
        </div>

        <!-- Pending Verifikasi -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600 mr-4">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Verifikasi</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ \App\Models\PengajuanKgb::whereIn('status', ['verifikasi_kabupaten'])->count() }}</p>
                </div>
            </div>
            <a href="{{ route('filament.admin.resources.pengajuan-kgb.index', ['status' => 'verifikasi_kabupaten']) }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Verifikasi sekarang</a>
        </div>

        <!-- Approval Rate -->
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600 mr-4">
                    <x-heroicon-o-check-circle class="w-6 h-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Approval Rate</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format(\App\Models\PengajuanKgb::whereNotNull('status')->count() > 0 ? (\App\Models\PengajuanKgb::where('status', 'disetujui')->count() / \App\Models\PengajuanKgb::whereNotNull('status')->count()) * 100 : 0, 1) }}%</p>
                </div>
            </div>
            <a href="{{ route('filament.admin.resources.pengajuan-kgb.index') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">Lihat analisis</a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Pengajuan per Dinas - Bar Chart -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pengajuan per Dinas (Bulan Ini)</h2>
            <div id="pengajuanPerDinasChart" class="h-80"></div>
        </div>

        <!-- Trend Pengajuan 6 Bulan - Line Chart -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Trend Pengajuan 6 Bulan Terakhir</h2>
            <div id="trendPengajuanChart" class="h-80"></div>
        </div>
    </div>

    <!-- Status Distribution - Donut Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Status Pengajuan</h2>
            <div id="statusDistributionChart" class="h-80"></div>
        </div>
    </div>

    <!-- Recent Activities Table -->
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Aktivitas Terbaru</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dinas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pegawai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach(\App\Models\PengajuanKgb::with(['tenant', 'pegawai', 'userPengaju'])->latest()->limit(10)->get() as $pengajuan)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->created_at->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $pengajuan->tenant->nama ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->pegawai->nama ?? 'N/A' }} ({{ $pengajuan->pegawai->nip ?? 'N/A' }})</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Pengajuan KGB</td>
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $pengajuan->userPengaju->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance Ranking Table -->
    <div class="bg-white rounded-xl shadow p-6 mt-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Peringkat Dinas Berdasarkan Approval Rate</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dinas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disetujui</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ditolak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate (%)</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $rank = 1;
                        $tenants = \App\Models\Tenant::with(['pengajuanKgbs'])->get()->map(function($tenant) {
                            $total = $tenant->pengajuanKgbs->count();
                            $approved = $tenant->pengajuanKgbs->where('status', 'disetujui')->count();
                            $rejected = $tenant->pengajuanKgbs->whereIn('status', ['ditolak', 'ditolak_dinas', 'ditolak_kabupaten'])->count();
                            $rate = $total > 0 ? ($approved / $total) * 100 : 0;
                            
                            return [
                                'tenant' => $tenant,
                                'total' => $total,
                                'approved' => $approved,
                                'rejected' => $rejected,
                                'rate' => $rate,
                            ];
                        })->sortByDesc('rate');
                    @endphp
                    
                    @foreach($tenants as $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $rank++ }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $data['tenant']->nama }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['total'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['approved'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $data['rejected'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="text-sm font-medium text-gray-900 mr-2">{{ number_format($data['rate'], 1) }}%</span>
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $data['rate'] }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart initialization code will go here
        document.addEventListener('DOMContentLoaded', function() {
            // Pengajuan per Dinas - Bar Chart
            const pengajuanPerDinasCtx = document.getElementById('pengajuanPerDinasChart').getContext('2d');
            new Chart(pengajuanPerDinasCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @php
                            $dinasData = \App\Models\PengajuanKgb::whereMonth('created_at', now()->month)
                                ->with('tenant')
                                ->get()
                                ->groupBy('tenant_id')
                                ->map(function($items) {
                                    return $items->first()->tenant->nama ?? 'Unknown';
                                })
                                ->take(5);
                            echo $dinasData->join('","');
                        @endphp
                    ],
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: [
                            @php
                                $pengajuanCounts = \App\Models\PengajuanKgb::whereMonth('created_at', now()->month)
                                    ->with('tenant')
                                    ->get()
                                    ->groupBy('tenant_id')
                                    ->map(function($items) {
                                        return $items->count();
                                    })
                                    ->take(5);
                                echo $pengajuanCounts->join(',');
                            @endphp
                        ],
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
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

            // Trend Pengajuan 6 Bulan - Line Chart
            const trendPengajuanCtx = document.getElementById('trendPengajuanChart').getContext('2d');
            new Chart(trendPengajuanCtx, {
                type: 'line',
                data: {
                    labels: [
                        @php
                            $months = [];
                            for ($i = 5; $i >= 0; $i--) {
                                $months[] = now()->subMonths($i)->format('M Y');
                            }
                            echo '"' . implode('","', $months) . '"';
                        @endphp
                    ],
                    datasets: [{
                        label: 'Diajukan',
                        data: [
                            @php
                                $submittedData = [];
                                for ($i = 5; $i >= 0; $i--) {
                                    $month = now()->subMonths($i)->month;
                                    $year = now()->subMonths($i)->year;
                                    $submittedData[] = \App\Models\PengajuanKgb::whereMonth('created_at', $month)
                                        ->whereYear('created_at', $year)
                                        ->where('status', '!=', 'draft')
                                        ->count();
                                }
                                echo implode(',', $submittedData);
                            @endphp
                        ],
                        borderColor: 'rgb(79, 70, 229)',
                        tension: 0.1
                    }, {
                        label: 'Disetujui',
                        data: [
                            @php
                                $approvedData = [];
                                for ($i = 5; $i >= 0; $i--) {
                                    $month = now()->subMonths($i)->month;
                                    $year = now()->subMonths($i)->year;
                                    $approvedData[] = \App\Models\PengajuanKgb::whereMonth('created_at', $month)
                                        ->whereYear('created_at', $year)
                                        ->where('status', 'disetujui')
                                        ->count();
                                }
                                echo implode(',', $approvedData);
                            @endphp
                        ],
                        borderColor: 'rgb(16, 185, 129)',
                        tension: 0.1
                    }, {
                        label: 'Ditolak',
                        data: [
                            @php
                                $rejectedData = [];
                                for ($i = 5; $i >= 0; $i--) {
                                    $month = now()->subMonths($i)->month;
                                    $year = now()->subMonths($i)->year;
                                    $rejectedData[] = \App\Models\PengajuanKgb::whereMonth('created_at', $month)
                                        ->whereYear('created_at', $year)
                                        ->whereIn('status', ['ditolak', 'ditolak_dinas', 'ditolak_kabupaten'])
                                        ->count();
                                }
                                echo implode(',', $rejectedData);
                            @endphp
                        ],
                        borderColor: 'rgb(239, 68, 68)',
                        tension: 0.1
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

            // Status Distribution - Doughnut Chart
            const statusDistributionCtx = document.getElementById('statusDistributionChart').getContext('2d');
            new Chart(statusDistributionCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Draft', 'Diajukan', 'Verifikasi Dinas', 'Verifikasi Kabupaten', 'Disetujui', 'Ditolak'],
                    datasets: [{
                        data: [
                            {{ \App\Models\PengajuanKgb::where('status', 'draft')->count() }},
                            {{ \App\Models\PengajuanKgb::where('status', 'diajukan')->count() }},
                            {{ \App\Models\PengajuanKgb::where('status', 'verifikasi_dinas')->count() }},
                            {{ \App\Models\PengajuanKgb::where('status', 'verifikasi_kabupaten')->count() }},
                            {{ \App\Models\PengajuanKgb::where('status', 'disetujui')->count() }},
                            {{ \App\Models\PengajuanKgb::whereIn('status', ['ditolak', 'ditolak_dinas', 'ditolak_kabupaten'])->count() }}
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
    </script>
</div>