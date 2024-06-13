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

                        <div class="d-flex align-items-center mb-2 p-3">
                            <input type="text" class="form-control border p-2 me-2" name="searchbox" id="searchbox" placeholder="Search..." style="max-width: 300px;" autofocus>
                            <button id="openModalButton" class="btn btn-danger my-3">
                                <i class="fas fa-camera"></i>
                            </button>
                            <input type="number" class="form-control border p-2 ms-2" name="yearFilter" id="yearFilter" placeholder="Filter by Year" style="max-width: 150px;">
                            <select class="form-select border p-2 ms-2" name="statusFilter" id="statusFilter" style="max-width: 150px;">
                                <option value="">Filter by Status</option>
                                <option value="Good">Good</option>
                                <option value="Repair">Repair</option>
                                <option value="Breakdown">Breakdown</option>
                                <option value="Dispose">Dispose</option>
                                <!-- Tambahkan opsi status lainnya sesuai kebutuhan -->
                            </select>
                            <div class="ms-auto my-3">
                                <button id="exportExcelButton" class="btn bg-gradient-dark mb-0">
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
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kategori Asset') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Asset Position') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Jenis') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Description') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Perolehan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (Hari)') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Dept') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Kerusakan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Pengembalian') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Penghapusan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Remarks') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventoryData as $inventory)
                                        <tr class="text-center text-xxs">
                                            <td>{{ $inventory->asset_code ?? '-' }}</td>
                                            <td>{{ $inventory->asset_category ?? '-' }}</td>
                                            <td>{{ $inventory->asset_position_dept ?? '-' }}</td>
                                            <td>{{ $inventory->asset_type ?? '-' }}</td>
                                            <td>{{ $inventory->description ?? '-' }}</td>
                                            <td>{{ $inventory->serial_number ?? '-' }}</td>
                                            <td>{{ $inventory->location ?? '-' }}</td>
                                            <td>{{ date('Y-m-d', strtotime($inventory->acquisition_date)) ?? '-' }}</td>
                                            <?php
                                            $acquisitionDate = new DateTime($inventory->acquisition_date);
                                            $usefulLife = $inventory->useful_life * 365; // Convert useful life from years to days
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
                                            ?>
                                            <td>{{ $message }}</td>
                                            <td>{{ $inventory->user ?? '-' }}</td>
                                            <td>{{ $inventory->dept ?? '-' }}</td>
                                            <td>{{ $inventory->status ?? '-' }}</td>
                                            <td>{{ $inventory->tanggal_kerusakan ?? '-' }}</td>
                                            <td>{{ $inventory->tanggal_pengembalian ?? '-' }}</td>
                                            <td>{{ $inventory->tanggal_penghapusan ?? '-' }}</td>
                                            <td>{{ $inventory->remarks ?? '-' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            var table = $('#inventoryTable').DataTable({
                "paging": false,
                "pageLength": -1,
                "columnDefs": [{
                        "orderable": true,
                        "targets": 7
                    }, // Enable ordering on the 8th column (index 7)
                    {
                        "orderable": false,
                        "targets": '_all'
                    } // Disable ordering on all other columns
                ],
                "order": [
                    [7, 'desc']
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
                    table.columns(7).search('^' + year, true, false).draw();
                } else {
                    table.columns(7).search('').draw();
                }
            });

            // Filter by status functionality
            $('#statusFilter').on('change', function() {
                var status = $(this).val().trim();
                if (status !== '') {
                    table.columns(11).search(status).draw();
                } else {
                    table.columns(11).search('').draw();
                }
            });

            // Export to Excel functionality
            $('#exportExcelButton').on('click', function() {
                const sheetName = 'Report';
                const fileName = 'report_inventory';

                const table = document.getElementById('inventoryTable');

                // Memastikan tabel ditemukan sebelum melanjutkan
                if (!table) {
                    console.error('Tabel tidak ditemukan.');
                    return;
                }

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);

                XLSX.utils.book_append_sheet(wb, ws, sheetName);

                XLSX.writeFile(wb, fileName + '.xlsx');
            });
        });
    </script>

    <script>
        document.getElementById('openModalButton').addEventListener('click', function() {
            var modal = document.getElementById('myModal');
            modal.style.display = "block";
            startScanner();
        });

        document.getElementsByClassName('close')[0].addEventListener('click', function() {
            var modal = document.getElementById('myModal');
            modal.style.display = "none";
            Quagga.stop();
        });

        window.onclick = function(event) {
            var modal = document.getElementById('myModal');
            if (event.target == modal) {
                modal.style.display = "none";
                Quagga.stop();
            }
        };

        function startScanner() {
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: document.querySelector('#interactive'), // Target the div element, not the video directly
                    constraints: {
                        facingMode: "environment" // Ensure back camera is used
                    }
                },
                decoder: {
                    readers: [
                        "code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader",
                        "codabar_reader", "upc_reader", "upc_e_reader", "i2of5_reader"
                    ]
                }
            }, function(err) {
                if (err) {
                    console.error(err);
                    return;
                }
                console.log("Initialization finished. Ready to start");
                Quagga.start();
            });

            Quagga.onDetected(function(result) {
                if (result.codeResult) {
                    var code = result.codeResult.code;
                    document.getElementById('result').innerText = 'Barcode detected: ' + code;

                    // Set the code in the search box
                    var searchBox = document.getElementById('searchbox');
                    searchBox.value = code;

                    // Search the table
                    var table = $('#inventoryTable').DataTable();
                    table.search(code).draw();

                    // Close the modal
                    var modal = document.getElementById('myModal');
                    modal.style.display = "none";
                    Quagga.stop();
                }
            });
        }
    </script>

</x-layout>