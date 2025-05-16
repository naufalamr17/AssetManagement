<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="DASHBOARD ASSET MANAGEMENT"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="chart">
                                    <canvas id="pieChart" class="chart-canvas" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 ">Status Asset</h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="chart">
                                    <canvas id="stackedBarChart" class="chart-canvas" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 "> Status Asset per Kategori </h6>
                        </div>
                    </div>
                </div>
                @if (Auth::check() && Auth::user()->status == 'Administrator' || Auth::user()->status == 'Super Admin' || Auth::user()->status == 'Auditor' || Auth::user()->hirar == 'Manager' || Auth::user()->hirar == 'Deputy General Manager')
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="chart">
                                    <canvas id="yearlyGrowthChartSpecial" class="chart-canvas" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 "> Pertumbuhan Asset Pertahun </h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="chart">
                                    <canvas id="monthlyGrowthChartSpecial" class="chart-canvas" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 "> Pertumbuhan Asset Perbulan </h6>
                        </div>
                    </div>
                </div>
                @else
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="chart">
                                    <canvas id="yearlyGrowthChart" class="chart-canvas" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 "> Pertumbuhan Asset Pertahun </h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="chart">
                                    <canvas id="monthlyGrowthChart" class="chart-canvas" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 "> Pertumbuhan Asset Perbulan </h6>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-lg-8 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="table-responsive p-0" style="height: 250px;">
                                    <table id="inventoryTable" class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kode Asset') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Jenis') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (hari)') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Kerusakan') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Pengembalian') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Remarks') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($repair as $item)
                                            <tr class="text-center text-xxs">
                                                <td>{{ $item->asset_code ?? '-' }}</td>
                                                <td>{{ $item->asset_type ?? '-' }}</td>
                                                <td>{{ $item->serial_number ?? '-' }}</td>
                                                <?php
                                                if ($item->acquisition_date === '-') {
                                                    $message = "Tanggal tidak terdefinisi";
                                                } else {
                                                    $acquisitionDate = new DateTime($item->acquisition_date);
                                                    $usefulLife = $item->useful_life * 365; // Convert useful life from years to days
                                                    $endOfUsefulLife = clone $acquisitionDate;
                                                    $endOfUsefulLife->modify("+{$usefulLife} days");

                                                    $currentDate = new DateTime();
                                                    $interval = $currentDate->diff($endOfUsefulLife);

                                                    if ($currentDate > $endOfUsefulLife) {
                                                        $remainingDays = -$interval->days; // Use negative value for overdue days
                                                    } else {
                                                        $remainingDays = $interval->days;
                                                    }

                                                    $message = "{$remainingDays} hari";
                                                }
                                                ?>
                                                <td>{{ $message }}</td>
                                                <td>{{ $item->location ?? '-' }}</td>
                                                <td>{{ $item->status ?? '-' }}</td>
                                                <td>{{ $item->tanggal_kerusakan ?? '-' }}</td>
                                                <td>{{ $item->tanggal_pengembalian ?? '-' }}</td>
                                                <td>{{ $item->note ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 "> Repair & Breakdown Asset </h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-md-6 mt-4 mb-4">
                    <div class="card z-index-2">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
                            <div class="bg-white shadow-dark border-radius-lg py-3 ps-2 pe-2">
                                <div class="table-responsive p-0" style="height: 250px;">
                                    <table id="inventoryTable" class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kode Asset') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Jenis') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (hari)') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Penghapusan') }}</th>
                                                <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Remarks') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($inventory as $item)
                                            <tr class="text-center text-xxs">
                                                <td>{{ $item->asset_code ?? '-' }}</td>
                                                <td>{{ $item->asset_type ?? '-' }}</td>
                                                <td>{{ $item->serial_number ?? '-' }}</td>
                                                <?php
                                                if ($item->acquisition_date === '-') {
                                                    $message = "Tanggal tidak terdefinisi";
                                                } else {
                                                    $acquisitionDate = new DateTime($item->acquisition_date);
                                                    $usefulLife = $item->useful_life * 365; // Convert useful life from years to days
                                                    $endOfUsefulLife = clone $acquisitionDate;
                                                    $endOfUsefulLife->modify("+{$usefulLife} days");

                                                    $currentDate = new DateTime();
                                                    $interval = $currentDate->diff($endOfUsefulLife);

                                                    if ($currentDate > $endOfUsefulLife) {
                                                        $remainingDays = -$interval->days; // Use negative value for overdue days
                                                    } else {
                                                        $remainingDays = $interval->days;
                                                    }

                                                    $message = "{$remainingDays} hari";
                                                }
                                                ?>
                                                <td>{{ $message }}</td>
                                                <td>{{ $item->location ?? '-' }}</td>
                                                <td>{{ $item->status ?? '-' }}</td>
                                                <td>{{ $item->tanggal_penghapusan ?? '-' }}</td>
                                                <td>{{ $item->note ?? '-' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0 "> Dispose Asset </h6>
                        </div>
                    </div>
                </div>
                <x-footers.auth></x-footers.auth>
            </div>
        </div>
    </main>
    <x-plugins></x-plugins>
    </div>
    @push('js')
    <script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <script>
        const statusCounts = @json($statusCounts);
        const categoryStatusCounts = @json($categoryStatusCounts);

        // Pie Chart Data
        const statusLabels = ['Good', 'Repair', 'Breakdown'];
        const statusColors = {
            Good: '#4CAF50', // Green
            Repair: '#FFC107', // Yellow
            Breakdown: '#F44336' // Red
        };

        // Ensure the data and colors align with the status labels
        const pieData = {
            labels: statusLabels,
            datasets: [{
                label: 'Asset Status',
                data: statusLabels.map(label => statusCounts[label] || 0),
                backgroundColor: statusLabels.map(label => statusColors[label]),
            }]
        };

        // Calculate total count for pie chart
        const totalStatusCount = pieData.datasets[0].data.reduce((a, b) => a + b, 0);

        // Pie Chart Config
        const pieConfig = {
            type: 'pie',
            data: pieData,
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const value = pieData.datasets[0].data[tooltipItem.dataIndex];
                                const percentage = ((value / totalStatusCount) * 100).toFixed(2);
                                if (percentage === '0.00') return '';
                                return `${tooltipItem.label}: ${value} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            let percentage = ((value / totalStatusCount) * 100).toFixed(2);
                            if (percentage === '0.00') return '';
                            return percentage + '%';
                        },
                        color: '#fff',
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            },
            plugins: [ChartDataLabels]
        };

        // Render Pie Chart
        const pieChart = new Chart(
            document.getElementById('pieChart'),
            pieConfig
        );

        // Stacked Bar Chart Data
        const labels = Object.keys(categoryStatusCounts);
        const goodData = labels.map(label => categoryStatusCounts[label]['Good'] || 0);
        const brokenData = labels.map(label => categoryStatusCounts[label]['Breakdown'] || 0);
        const repairData = labels.map(label => categoryStatusCounts[label]['Repair'] || 0);

        const stackedBarData = {
            labels: labels,
            datasets: [{
                    label: 'Good',
                    data: goodData,
                    backgroundColor: '#4CAF50'
                },
                {
                    label: 'Repair',
                    data: repairData,
                    backgroundColor: '#FFC107'
                },
                {
                    label: 'Breakdown',
                    data: brokenData,
                    backgroundColor: '#F44336'
                },
            ]
        };

        // Calculate total count for each category
        const totalCategoryCounts = labels.map(label =>
            (categoryStatusCounts[label]['Good'] || 0) +
            (categoryStatusCounts[label]['Repair'] || 0) +
            (categoryStatusCounts[label]['Breakdown'] || 0)
        );

        // Stacked Bar Chart Config
        const stackedBarConfig = {
            type: 'bar',
            data: stackedBarData,
            options: {
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                const datasetLabel = tooltipItem.dataset.label;
                                const value = tooltipItem.raw;
                                const categoryIndex = tooltipItem.dataIndex;
                                const total = totalCategoryCounts[categoryIndex];
                                const percentage = ((value / total) * 100).toFixed(2);
                                if (percentage === '0.00') return '';
                                return `${datasetLabel}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        };

        // Render Stacked Bar Chart
        const stackedBarChart = new Chart(
            document.getElementById('stackedBarChart'),
            stackedBarConfig
        );

        const yearlyGrowth = @json($yearlyGrowth);

        const yearlabels = yearlyGrowth.map(item => item.year);
        const data = yearlyGrowth.map(item => item.count);

        const yearlyGrowthChartElem = document.getElementById('yearlyGrowthChart');
        if (yearlyGrowthChartElem) {
            const ctx = yearlyGrowthChartElem.getContext('2d');
            const yearlyGrowthChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: yearlabels,
                    datasets: [{
                        label: 'Asset Growth Per Year',
                        data: data,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#222',
                            font: {
                                weight: 'bold',
                                size: 7 // label lebih kecil
                            },
                            formatter: function(value) {
                                return value;
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                },
                plugins: [ChartDataLabels]
            });
        }

        const monthlyGrowthChartElem = document.getElementById('monthlyGrowthChart');
        if (monthlyGrowthChartElem) {
            var ctxMonthly = monthlyGrowthChartElem.getContext('2d');
            var monthlyGrowthChart = new Chart(ctxMonthly, {
                type: 'bar',
                data: {
                    labels: @json($monthlyGrowth -> pluck('month')),
                    datasets: [{
                        label: 'Asset Growth Per Month',
                        data: @json($monthlyGrowth -> pluck('count')),
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#222',
                            font: {
                                weight: 'bold',
                                size: 7 // label lebih kecil
                            },
                            formatter: function(value) {
                                return value.y !== undefined ? value.y : value;
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                },
                plugins: [ChartDataLabels]
            });
        }

        // Data yang dikirim dari controller
        const yearlyGrowthSpecial = @json($yearlyGrowth);

        // Mengelompokkan data berdasarkan lokasi
        const groupedData = {};
        const locationColors = {
            'Head Office': '#FF5733', // Misalnya warna oranye
            'Office Kendari': '#33FF57', // Misalnya warna hijau
            'Site Molore': '#5733FF' // Misalnya warna biru
        };

        yearlyGrowthSpecial.forEach(item => {
            if (Object.keys(locationColors).includes(item.location)) {
                if (!groupedData[item.location]) {
                    groupedData[item.location] = {
                        label: item.location,
                        data: [],
                        backgroundColor: locationColors[item.location],
                        borderColor: locationColors[item.location],
                        borderWidth: 2,
                        fill: false
                    };
                }
                groupedData[item.location].data.push({
                    x: item.year,
                    y: item.count
                });
            }
        });

        // Memisahkan labels tahun
        const yearLabels = [...new Set(yearlyGrowthSpecial.map(item => item.year))].sort();

        // Normalisasi data: pastikan setiap lokasi punya data untuk semua tahun
        Object.keys(groupedData).forEach(loc => {
            const dataMap = {};
            groupedData[loc].data.forEach(d => {
                dataMap[d.x] = d.y;
            });
            groupedData[loc].data = yearLabels.map(year => dataMap[year] !== undefined ? dataMap[year] : 0);
        });

        // Membuat array objek dari groupedData
        const datasets = Object.values(groupedData);

        // Inisialisasi Chart.js
        const yearlyGrowthChartSpecialElem = document.getElementById('yearlyGrowthChartSpecial');
        if (yearlyGrowthChartSpecialElem) {
            const ctx2 = yearlyGrowthChartSpecialElem.getContext('2d');
            const yearlyGrowthChartSpecial = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: yearLabels,
                    datasets: datasets
                },
                options: {
                    scales: {
                        x: {
                            type: 'category',
                            title: {
                                display: true,
                                text: 'Year'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: false,
                                text: 'Count'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#222',
                            font: {
                                weight: 'bold',
                                size: 7 // label lebih kecil
                            },
                            formatter: function(value) {
                                return value.y !== undefined ? value.y : value;
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                },
                plugins: [ChartDataLabels]
            });
        }

        // Data yang dikirim dari controller
        const monthlyGrowthSpecial = @json($monthlyGrowth);

        // Mengelompokkan data berdasarkan lokasi
        const groupedData2 = {};
        const locationColors2 = {
            'Head Office': '#FF5733', // Misalnya warna oranye
            'Office Kendari': '#33FF57', // Misalnya warna hijau
            'Site Molore': '#5733FF' // Misalnya warna biru
        };

        monthlyGrowthSpecial.forEach(item => {
            if (Object.keys(locationColors2).includes(item.location)) {
                if (!groupedData2[item.location]) {
                    groupedData2[item.location] = {
                        label: item.location,
                        data: [],
                        backgroundColor: locationColors2[item.location],
                        borderColor: locationColors2[item.location],
                        borderWidth: 2,
                        fill: false
                    };
                }
                groupedData2[item.location].data.push({
                    x: item.month,
                    y: item.count
                });
            }
        });

        // Memisahkan labels tahun
        // Dapatkan label tahun dari data bulanan yang disediakan
        const yearLabels2 = monthlyGrowthSpecial
            .map(item => item.year) // Ambil semua nilai tahun dari setiap item
            .filter(year => year !== undefined) // Hapus nilai undefined

        // Membuat array objek dari groupedData2 (bukan dari groupedData)
        const datasets2 = Object.values(groupedData2);

        // Inisialisasi Chart.js
        const monthlyGrowthChartSpecialElem = document.getElementById('monthlyGrowthChartSpecial');
        if (monthlyGrowthChartSpecialElem) {
            const ctx3 = monthlyGrowthChartSpecialElem.getContext('2d');
            const monthlyGrowthChartSpecial = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: yearLabels2,
                    datasets: datasets2
                },
                options: {
                    scales: {
                        x: {
                            type: 'category',
                            title: {
                                display: true,
                                text: 'Year'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: false,
                                text: 'Count'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                        },
                        datalabels: {
                            anchor: 'end',
                            align: 'end',
                            color: '#222',
                            font: {
                                weight: 'bold',
                                size: 7 // label lebih kecil
                            },
                            formatter: function(value) {
                                return value.y !== undefined ? value.y : value;
                            }
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false
                },
                plugins: [ChartDataLabels]
            });
        }
    </script>

    @endpush
</x-layout>