<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- Custom CSS to make the DataTable smaller -->
    <style>
        #inventoryTable_wrapper .dataTables_length,
        #inventoryTable_wrapper .dataTables_filter,
        #inventoryTable_wrapper .dataTables_info,
        #inventoryTable_wrapper .dataTables_paginate {
            font-size: 0.75rem;
        }

        #inventoryTable {
            font-size: 0.75rem;
        }

        #inventoryTable th,
        #inventoryTable td {
            padding: 4px 8px;
        }

        /* CSS to make the table scrollable */
        .table-responsive {
            max-height: 500px;
            /* Set the desired maximum height */
            overflow-y: auto;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
    <style>
        #interactive {
            width: 100%;
            height: 400px;
            overflow: hidden;
            position: relative;
        }

        #interactive video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #result {
            margin-top: 20px;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        /* Media query for landscape orientation on mobile devices */
        @media only screen and (max-width: 600px) {
            .modal-content {
                width: 90%;
                max-width: none;
                height: 60vh;
                overflow-y: auto;
            }
        }
    </style>

    <x-navbars.sidebar activePage="report"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="REPORT"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                        @endif

                        <div class="d-flex align-items-center flex-wrap mb-2 p-3">
                            <div class="mb-2 me-2">
                                <input type="text" class="form-control border p-2" name="searchbox" id="searchbox" placeholder="Search...">
                            </div>
                            <div class="mb-2 me-2 mt-3">
                                <button id="openModalButton" class="btn btn-danger">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            <div class="mb-2 me-2">
                                <input type="number" class="form-control border p-2" name="yearFilter" id="yearFilter" placeholder="Filter by Year">
                            </div>
                            <!-- Tambahkan filter range tanggal perolehan -->
                            <div class="mb-2 me-2">
                                <input type="date" class="form-control border p-2" name="startDateFilter" id="startDateFilter" placeholder="Tanggal Perolehan Dari">
                            </div>
                            <div class="mb-2 me-2">
                                <input type="date" class="form-control border p-2" name="endDateFilter" id="endDateFilter" placeholder="Tanggal Perolehan Sampai">
                            </div>
                            <div class="mb-2 me-2">
                                <select class="form-control border p-2" name="statusFilter" id="statusFilter">
                                    <option value="">Filter by Status</option>
                                    <option value="Good">Good</option>
                                    <option value="Repair">Repair</option>
                                    <option value="Breakdown">Breakdown</option>
                                    <option value="Dispose">Dispose</option>
                                    <!-- Tambahkan opsi status lainnya sesuai kebutuhan -->
                                </select>
                            </div>
                            <div class="mb-2 me-2">
                                <select class="form-control border p-2" name="locationFilter" id="locationFilter">
                                    <option value="">Filter by Location</option>
                                    <option value="Head Office">Head Office</option>
                                    <option value="Office Kendari">Office Kendari</option>
                                    <option value="Site Molore">Site Molore</option>
                                    <!-- Tambahkan opsi status lainnya sesuai kebutuhan -->
                                </select>
                            </div>
                            <div class="ms-auto mb-2">
                                <button id="exportExcelButton" class="btn bg-gradient-dark">
                                    <i class="material-icons text-sm">file_download</i>&nbsp;&nbsp;Download Excel
                                </button>
                            </div>

                            <!-- The Modal -->
                            <div id="myModal" class="modal">
                                <div class="modal-content">
                                    <span class="close">&times;</span>
                                    <div id="interactive" class="viewport"></div>
                                    <div id="result"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body px-2 pb-2">
                            <div class="table-responsive p-0">
                                <table id="inventoryTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kode Asset') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kode Asset Lama') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kategori Asset') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Asset Position') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Jenis') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Merk') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Description') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Perolehan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Useful Life (Tahun)') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (Hari)') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Dept') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Barcode Availability') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Kerusakan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Pengembalian') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Penghapusan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Remarks') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventoryData as $inventory)
                                        <tr class="text-center" style="font-size: 14px;">
                                            <td>{{ strtoupper($inventory->asset_code ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->old_asset_code ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->asset_category ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->asset_position_dept ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->asset_type ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->merk ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->description ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->serial_number ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->location ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->acquisition_date ?? '-') }}</td>
                                            <td>{{ $inventory->useful_life ? strtoupper($inventory->useful_life . ' TAHUN') : '-' }}</td>
                                            <?php
                                            if ($inventory->acquisition_date === '-') {
                                                $message = "TANGGAL TIDAK TERDEFINISI";
                                            } else {
                                                $acquisitionDate = new DateTime($inventory->acquisition_date);
                                                $usefulLife = $inventory->useful_life * 365; // Ubah umur manfaat dari tahun ke hari
                                                $endOfUsefulLife = clone $acquisitionDate;
                                                $endOfUsefulLife->modify("+{$usefulLife} days");

                                                $currentDate = new DateTime();
                                                $interval = $currentDate->diff($endOfUsefulLife);

                                                if ($currentDate > $endOfUsefulLife) {
                                                    $remainingDays = -$interval->days; // Nilai negatif untuk hari keterlambatan
                                                } else {
                                                    $remainingDays = $interval->days;
                                                }

                                                $message = strtoupper("{$remainingDays} HARI");
                                            }
                                            ?>
                                            <td>{{ $message }}</td>
                                            <td>{{ strtoupper($inventory->user ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->dept ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->status ?? '-') }}</td>
                                            <td>{{ strtoupper(!empty($inventory->barcode_availability) ? $inventory->barcode_availability : '-') }}</td>
                                            <td>{{ strtoupper($inventory->tanggal_kerusakan ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->tanggal_pengembalian ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->tanggal_penghapusan ?? '-') }}</td>
                                            <td>{{ strtoupper($inventory->remarks ?? '-') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Container untuk chart dan summary -->
            <div class="card mb-4">
                <div class="card-body py-3">
                    <div class="row align-items-center">
                        <div class="col-md-5 mb-3 mb-md-0 d-flex justify-content-center">
                            <div style="width:220px;max-width:100%;">
                                <canvas id="jenisAssetChart" height="180" style="max-height:180px;"></canvas>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div id="jenisAssetSummary" class="fs-6 mb-2">
                                <span>Total Asset: <b id="totalAssetCount">0</b></span><br>
                                <span>Jenis Asset "<b id="selectedJenis"></b>": <b id="jenisAssetCount">0</b></span><br>
                                <span>Persentase: <b id="jenisAssetPercent">0%</b></span>
                            </div>
                            <div>
                                <label for="jenisAssetSelect" class="form-label mb-1">Pilih Jenis Asset:</label>
                                <select id="jenisAssetSelect" class="form-select w-auto d-inline-block p-2" style="min-width:120px"></select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <!-- Tambahkan Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            var table = $('#inventoryTable').DataTable({
                "paging": false,
                "pageLength": -1,
                "columnDefs": [{
                        "orderable": true,
                        "targets": 9
                    }, // Enable ordering on the 8th column (index 7)
                    {
                        "orderable": false,
                        "targets": '_all'
                    } // Disable ordering on all other columns
                ],
                "order": [
                    [9, 'desc']
                ],
                "dom": '<"top">rt<"bottom"ip><"clear">',
            });

            // Add the search functionality
            $('#searchbox').on('keyup', function() {
                table.search(this.value).draw();

                if (this.value.length >= 13) {
                    setTimeout(() => {
                        this.select(); // Seleksi seluruh teks di dalam kotak pencarian
                    }, 2000);
                }
            });

            // Filter by year functionality
            $('#yearFilter').on('keyup', function() {
                var year = $(this).val().trim();
                if (year !== '') {
                    table.columns(9).search('^' + year, true, false).draw();
                } else {
                    table.columns(9).search('').draw();
                }
            });

            // Filter by date range functionality
            $('#startDateFilter, #endDateFilter').on('change', function() {
                var startDate = $('#startDateFilter').val();
                var endDate = $('#endDateFilter').val();

                // Validate date format (YYYY-MM-DD)
                var datePattern = /^\d{4}-\d{2}-\d{2}$/;
                if (startDate && !datePattern.test(startDate)) {
                    alert('Tanggal awal tidak valid. Harap gunakan format YYYY-MM-DD.');
                    return;
                }
                if (endDate && !datePattern.test(endDate)) {
                    alert('Tanggal akhir tidak valid. Harap gunakan format YYYY-MM-DD.');
                    return;
                }

                // Filter DataTable
                table.draw();
            });

            // Override DataTables default filtering function
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = $('#startDateFilter').val();
                    var max = $('#endDateFilter').val();
                    var date = data[9]; // Kolom tanggal perolehan (acquisition_date)

                    if (
                        (min === '' && max === '') || // Jika tidak ada filter tanggal
                        (min === '' && date <= max) || // Jika hanya filter tanggal akhir
                        (min <= date && max === '') || // Jika hanya filter tanggal awal
                        (min <= date && date <= max) // Jika dalam rentang tanggal
                    ) {
                        return true;
                    }
                    return false;
                }
            );

            // Filter by status functionality
            $('#statusFilter').on('change', function() {
                var status = $(this).val().trim();
                if (status !== '') {
                    table.columns(14).search(status).draw();
                } else {
                    table.columns(14).search('').draw();
                }
            });

            // Filter by location functionality
            $('#locationFilter').on('change', function() {
                var status = $(this).val().trim();
                if (status !== '') {
                    table.columns(8).search(status).draw();
                } else {
                    table.columns(8).search('').draw();
                }
            });

            // Export to Excel functionality
            $('#exportExcelButton').on('click', function() {
                const sheetName = 'Report';
                const fileName = 'report_inventory';

                const table = document.getElementById('inventoryTable');

                if (!table) {
                    console.error('Tabel tidak ditemukan.');
                    return;
                }

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);

                const range = XLSX.utils.decode_range(ws['!ref']);

                for (let R = range.s.r + 1; R <= range.e.r; ++R) { // Looping setiap baris (lewati header)
                    const cellAddress = XLSX.utils.encode_cell({
                        r: R,
                        c: 9
                    }); // Mengambil kolom 'acquisition_date' (index 0)
                    const cell = ws[cellAddress];

                    if (cell && cell.t === 'n') { // Jika sel berisi angka (tipe 'n' untuk angka)
                        const dateValue = new Date((cell.v - 25569) * 86400 * 1000); // Konversi angka Excel ke Date
                        if (!isNaN(dateValue.getTime())) { // Pastikan tanggal valid
                            const day = String(dateValue.getUTCDate()).padStart(2, '0');
                            const month = String(dateValue.getUTCMonth() + 1).padStart(2, '0');
                            const year = dateValue.getUTCFullYear();
                            cell.v = `${day}-${month}-${year}`; // Format dd-mm-yyyy
                            cell.t = 's'; // Ubah tipe sel menjadi string
                        } else {
                            cell.v = '-'; // Jika tanggal tidak valid
                        }
                    }
                }

                // Autofit kolom
                const colWidths = [];
                for (let C = range.s.c; C <= range.e.c; ++C) {
                    let maxWidth = 0;
                    for (let R = range.s.r; R <= range.e.r; ++R) {
                        const cellAddress = XLSX.utils.encode_cell({
                            r: R,
                            c: C
                        });
                        if (!ws[cellAddress]) continue;
                        const cellTextLength = XLSX.utils.format_cell(ws[cellAddress]).length;
                        maxWidth = Math.max(maxWidth, cellTextLength);
                    }
                    colWidths[C] = {
                        wch: maxWidth + 2
                    };
                }
                ws['!cols'] = colWidths;

                XLSX.utils.book_append_sheet(wb, ws, sheetName);
                XLSX.writeFile(wb, fileName + '.xlsx');
            });

            // --- CHART & SUMMARY JENIS ASSET ---
            function getJenisAssetData() {
                var jenisCounts = {};
                var total = 0;
                table.rows({
                    search: 'applied'
                }).every(function() {
                    var jenis = this.data()[4] || '-';
                    jenisCounts[jenis] = (jenisCounts[jenis] || 0) + 1;
                    total++;
                });
                return {
                    jenisCounts,
                    total
                };
            }

            function updateJenisAssetSelect(jenisCounts) {
                var $select = $('#jenisAssetSelect');
                var current = $select.val();
                $select.empty();
                var jenisList = [];
                table.rows({
                    search: 'applied'
                }).every(function() {
                    var jenis = this.data()[4] || '-';
                    if (jenisList.indexOf(jenis) === -1) {
                        jenisList.push(jenis);
                    }
                });
                jenisList.sort();
                jenisList.forEach(function(jenis) {
                    $select.append($('<option>', {
                        value: jenis,
                        text: jenis
                    }));
                });
                if (current && jenisCounts[current]) {
                    $select.val(current);
                }
            }

            var jenisChart;

            function updateJenisAssetChart() {
                var {
                    jenisCounts,
                    total
                } = getJenisAssetData();
                updateJenisAssetSelect(jenisCounts);

                var selectedJenis = $('#jenisAssetSelect').val() || Object.keys(jenisCounts)[0] || '-';
                var selectedCount = jenisCounts[selectedJenis] || 0;
                var otherCount = total - selectedCount;

                var labels = [selectedJenis, 'Other'];
                var data = [selectedCount, otherCount];

                if (jenisChart) {
                    jenisChart.data.labels = labels;
                    jenisChart.data.datasets[0].data = data;
                    jenisChart.update();
                } else {
                    var ctx = document.getElementById('jenisAssetChart').getContext('2d');
                    jenisChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: [
                                    '#007bff', '#6c757d'
                                ],
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 16,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                updateJenisAssetSummary();
            }

            function updateJenisAssetSummary() {
                var {
                    jenisCounts,
                    total
                } = getJenisAssetData();
                var selectedJenis = $('#jenisAssetSelect').val() || Object.keys(jenisCounts)[0] || '-';
                var jenisCount = jenisCounts[selectedJenis] || 0;
                var percent = total > 0 ? ((jenisCount / total) * 100).toFixed(2) : 0;

                $('#totalAssetCount').text(total);
                $('#selectedJenis').text(selectedJenis);
                $('#jenisAssetCount').text(jenisCount);
                $('#jenisAssetPercent').text(percent + '%');
            }

            $('#jenisAssetSelect').on('change', updateJenisAssetChart);

            table.on('draw', function() {
                updateJenisAssetChart();
            });

            updateJenisAssetChart();

            // Inisialisasi pertama
            setTimeout(updateJenisAssetChart, 500);

        });
    </script>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <script>
        document.getElementById('openModalButton').addEventListener('click', function() {
            var modal = document.getElementById('myModal');
            modal.style.display = "block";
            startScanner();
        });

        document.getElementsByClassName('close')[0].addEventListener('click', function() {
            var modal = document.getElementById('myModal');
            modal.style.display = "none";
            html5QrCode.stop().catch(err => console.error(err));
        });

        window.onclick = function(event) {
            var modal = document.getElementById('myModal');
            if (event.target == modal) {
                modal.style.display = "none";
                html5QrCode.stop().catch(err => console.error(err));
            }
        };

        function startScanner() {
            const html5QrCode = new Html5Qrcode("interactive");

            html5QrCode.start({
                    facingMode: "environment"
                }, {
                    fps: 10, // Set the framerate to 10 frames per second
                    qrbox: {
                        width: 250,
                        height: 250
                    } // Set the dimensions of the QR code scanning box
                },
                (decodedText, decodedResult) => {
                    // Handle the result here
                    document.getElementById('result').innerText = 'QR Code detected: ' + decodedText;

                    // Set the code in the search box
                    var searchBox = document.getElementById('searchbox');
                    searchBox.value = decodedText.substring(0, 13);

                    // Close the modal
                    var modal = document.getElementById('myModal');
                    modal.style.display = "none";
                    html5QrCode.stop().catch(err => console.error(err));

                    // Search the table
                    var table = $('#inventoryTable').DataTable();
                    table.search(searchBox.value).draw();
                },
                (errorMessage) => {
                    // Handle error here
                    console.warn(`QR Code no longer in front of camera: ${errorMessage}`);
                }
            ).then(() => {
                // Apply video constraints after starting the scanner
                setTimeout(() => {
                    html5QrCode.applyVideoConstraints({
                        focusMode: "continuous",
                        advanced: [{
                            zoom: 2.0
                        }]
                    }).catch(err => console.error(err));
                }, 2000);
            }).catch(err => {
                // Start failed, handle it here
                console.error(`Unable to start scanning, error: ${err}`);
            });
        }
    </script>

</x-layout>