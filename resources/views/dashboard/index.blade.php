<x-layout bodyClass="g-sidenav-show">
    <x-navbars.sidebar activePage="dashboard"></x-navbars.sidebar>

    <main class="main-content position-relative">
        <x-navbars.navs.auth titlePage="Dashboard"></x-navbars.navs.auth>

        @php
            $totalAssets = collect($statusCounts)->sum();
            $statusPalette = [
                'Good' => '#20b486',
                'Repair' => '#f59e0b',
                'Breakdown' => '#ef5d6f',
                'Waiting Dispose' => '#8b5cf6',
                'Dispose' => '#7d8198',
            ];
            $badgeClass = fn ($status) => match ($status) {
                'Good' => 'status-good',
                'Repair' => 'status-repair',
                'Breakdown' => 'status-breakdown',
                'Waiting Dispose' => 'status-waiting',
                'Dispose' => 'status-dispose',
                default => 'status-neutral',
            };
            $remainingLife = function ($item) {
                if (empty($item->acquisition_date) || $item->acquisition_date === '-') {
                    return 'Tidak tersedia';
                }

                try {
                    $acquisitionDate = new DateTime($item->acquisition_date);
                    $endOfUsefulLife = (clone $acquisitionDate)->modify('+' . ((int) $item->useful_life) . ' years');
                    $days = (new DateTime())->diff($endOfUsefulLife)->days;
                    return (new DateTime()) > $endOfUsefulLife ? "-{$days} hari" : "{$days} hari";
                } catch (Throwable $exception) {
                    return 'Tidak tersedia';
                }
            };
        @endphp

        <div class="container-fluid py-4">
            <section class="dashboard-intro">
                <div>
                    <span class="dashboard-kicker">Ringkasan aset</span>
                    <h1>Selamat datang, {{ Auth::user()->name }}</h1>
                    <p>Pantau kondisi, pertumbuhan, perbaikan, dan penghapusan aset dalam satu tempat.</p>
                </div>
                <div class="company-chip">
                    <span class="company-chip-dot"></span>
                    PT {{ Auth::user()->status === 'Super Admin' ? 'MLP & KES' : (Auth::user()->company ?? 'MLP') }}
                </div>
            </section>

            <section class="metric-grid">
                <article class="metric-card metric-primary">
                    <div class="metric-icon"><i class="material-icons-round">inventory_2</i></div>
                    <div>
                        <span>Total aset</span>
                        <strong>{{ number_format($totalAssets) }}</strong>
                        <small>Seluruh status tercatat</small>
                    </div>
                </article>
                <article class="metric-card">
                    <div class="metric-icon metric-icon-success"><i class="material-icons-round">check_circle</i></div>
                    <div>
                        <span>Kondisi baik</span>
                        <strong>{{ number_format($statusCounts['Good'] ?? 0) }}</strong>
                        <small>Siap digunakan</small>
                    </div>
                </article>
                <article class="metric-card">
                    <div class="metric-icon metric-icon-warning"><i class="material-icons-round">build_circle</i></div>
                    <div>
                        <span>Dalam perbaikan</span>
                        <strong>{{ number_format($statusCounts['Repair'] ?? 0) }}</strong>
                        <small>Sedang ditangani</small>
                    </div>
                </article>
                <article class="metric-card">
                    <div class="metric-icon metric-icon-danger"><i class="material-icons-round">error</i></div>
                    <div>
                        <span>Breakdown</span>
                        <strong>{{ number_format($statusCounts['Breakdown'] ?? 0) }}</strong>
                        <small>Perlu perhatian</small>
                    </div>
                </article>
            </section>

            <section class="dashboard-grid">
                <article class="panel panel-status">
                    <header class="panel-header">
                        <div><span class="panel-kicker">Kondisi</span><h2>Komposisi status</h2></div>
                        <span class="panel-meta">{{ number_format($totalAssets) }} aset</span>
                    </header>
                    <div class="chart-wrap chart-doughnut"><canvas id="statusChart"></canvas></div>
                </article>

                <article class="panel panel-category">
                    <header class="panel-header">
                        <div><span class="panel-kicker">Kategori</span><h2>Status per kategori</h2></div>
                    </header>
                    <div class="chart-wrap"><canvas id="categoryChart"></canvas></div>
                </article>

                <article class="panel panel-growth">
                    <header class="panel-header">
                        <div><span class="panel-kicker">Tren</span><h2>Pertumbuhan bulanan</h2></div>
                        <span class="panel-meta">12 bulan terakhir</span>
                    </header>
                    <div class="chart-wrap"><canvas id="monthlyChart"></canvas></div>
                </article>

                <article class="panel panel-yearly">
                    <header class="panel-header">
                        <div><span class="panel-kicker">Historis</span><h2>Pertumbuhan tahunan</h2></div>
                    </header>
                    <div class="chart-wrap"><canvas id="yearlyChart"></canvas></div>
                </article>
            </section>

            <section class="panel data-panel">
                <header class="panel-header">
                    <div><span class="panel-kicker">Aktivitas terbaru</span><h2>Repair & breakdown</h2></div>
                    <a href="{{ route('repair_inventory') }}" class="panel-link">Lihat semua <i class="material-icons-round">arrow_forward</i></a>
                </header>
                <div class="table-responsive">
                    <table class="table sima-table">
                        <thead><tr><th>Kode aset</th><th>Jenis</th><th>Lokasi</th><th>Status</th><th>Sisa umur</th><th>Tanggal kerusakan</th><th>Catatan</th></tr></thead>
                        <tbody>
                            @forelse($repair as $item)
                            <tr>
                                <td><strong class="asset-code">{{ $item->asset_code ?? '-' }}</strong></td>
                                <td>{{ $item->asset_type ?? '-' }}<small class="cell-subtitle">{{ $item->serial_number ?? 'Tanpa serial' }}</small></td>
                                <td>{{ $item->location ?? '-' }}</td>
                                <td><span class="status-pill {{ $badgeClass($item->status) }}">{{ $item->status ?? '-' }}</span></td>
                                <td>{{ $remainingLife($item) }}</td>
                                <td>{{ $item->tanggal_kerusakan ?? '-' }}</td>
                                <td class="cell-truncate">{{ $item->note ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="7"><div class="empty-state"><i class="material-icons-round">task_alt</i><span>Belum ada aktivitas perbaikan.</span></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="panel data-panel">
                <header class="panel-header">
                    <div><span class="panel-kicker">Aktivitas terbaru</span><h2>Penghapusan aset</h2></div>
                    <a href="{{ route('dispose_inventory') }}" class="panel-link">Lihat semua <i class="material-icons-round">arrow_forward</i></a>
                </header>
                <div class="table-responsive">
                    <table class="table sima-table">
                        <thead><tr><th>Kode aset</th><th>Jenis</th><th>Lokasi</th><th>Status</th><th>Tanggal penghapusan</th><th>Catatan</th></tr></thead>
                        <tbody>
                            @forelse($inventory as $item)
                            <tr>
                                <td><strong class="asset-code">{{ $item->asset_code ?? '-' }}</strong></td>
                                <td>{{ $item->asset_type ?? '-' }}<small class="cell-subtitle">{{ $item->serial_number ?? 'Tanpa serial' }}</small></td>
                                <td>{{ $item->location ?? '-' }}</td>
                                <td><span class="status-pill {{ $badgeClass($item->status) }}">{{ $item->status ?? '-' }}</span></td>
                                <td>{{ $item->tanggal_penghapusan ?? '-' }}</td>
                                <td class="cell-truncate">{{ $item->note ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="6"><div class="empty-state"><i class="material-icons-round">inventory</i><span>Belum ada aktivitas penghapusan.</span></div></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>

    @push('js')
    <script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>
    <script>
        (() => {
            const statusCounts = @json($statusCounts);
            const categoryStatusCounts = @json($categoryStatusCounts);
            const monthlyGrowth = @json($monthlyGrowth);
            const yearlyGrowth = @json($yearlyGrowth);
            const palette = @json($statusPalette);
            const locationColors = {'Head Office': '#5b5ce2', 'Office Kendari': '#20b486', 'Site Molore': '#f59e0b'};

            Chart.defaults.font.family = "'DM Sans', sans-serif";
            Chart.defaults.color = '#727690';
            Chart.defaults.borderColor = '#ececf4';

            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {mode: 'index', intersect: false},
                plugins: {
                    legend: {position: 'bottom', labels: {usePointStyle: true, pointStyle: 'circle', padding: 18, boxWidth: 7}},
                    tooltip: {backgroundColor: '#16182f', padding: 12, cornerRadius: 9, displayColors: true}
                },
                scales: {
                    x: {grid: {display: false}, ticks: {maxRotation: 0, autoSkip: true, maxTicksLimit: 8}},
                    y: {beginAtZero: true, border: {display: false}, ticks: {precision: 0}}
                }
            };

            const statusLabels = Object.keys(statusCounts);
            new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {labels: statusLabels, datasets: [{data: statusLabels.map(label => statusCounts[label]), backgroundColor: statusLabels.map(label => palette[label] || '#a0a4b8'), borderWidth: 0, hoverOffset: 5}]},
                options: {responsive: true, maintainAspectRatio: false, cutout: '72%', plugins: baseOptions.plugins}
            });

            const categoryLabels = Object.keys(categoryStatusCounts);
            const allStatuses = [...new Set(categoryLabels.flatMap(label => Object.keys(categoryStatusCounts[label])))];
            new Chart(document.getElementById('categoryChart'), {
                type: 'bar',
                data: {labels: categoryLabels, datasets: allStatuses.map(status => ({label: status, data: categoryLabels.map(label => categoryStatusCounts[label][status] || 0), backgroundColor: palette[status] || '#a0a4b8', borderRadius: 5, borderSkipped: false}))},
                options: {...baseOptions, scales: {...baseOptions.scales, x: {...baseOptions.scales.x, stacked: true}, y: {...baseOptions.scales.y, stacked: true}}}
            });

            function growthData(items, key) {
                const labels = [...new Set(items.map(item => item[key]))].sort();
                const hasLocation = items.some(item => item.location);
                if (!hasLocation) return {labels, datasets: [{label: 'Aset baru', data: labels.map(label => items.find(item => item[key] === label)?.count || 0), borderColor: '#5b5ce2', backgroundColor: 'rgba(91,92,226,.12)', fill: true, tension: .38, pointRadius: 3, pointBackgroundColor: '#5b5ce2'}]};
                const locations = [...new Set(items.map(item => item.location).filter(Boolean))];
                return {labels, datasets: locations.map(location => ({label: location, data: labels.map(label => items.find(item => item[key] === label && item.location === location)?.count || 0), borderColor: locationColors[location] || '#7d8198', backgroundColor: locationColors[location] || '#7d8198', tension: .38, pointRadius: 2}))};
            }

            new Chart(document.getElementById('monthlyChart'), {type: 'line', data: growthData(monthlyGrowth, 'month'), options: baseOptions});
            new Chart(document.getElementById('yearlyChart'), {type: 'bar', data: growthData(yearlyGrowth, 'year'), options: {...baseOptions, plugins: baseOptions.plugins}});
        })();
    </script>
    @endpush
</x-layout>
