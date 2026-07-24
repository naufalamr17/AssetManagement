<x-layout bodyClass="g-sidenav-show  bg-gray-200">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <x-navbars.sidebar activePage="inventory"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Daftar Aset"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
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

                        <div class="page-toolbar">
                            <div class="toolbar-search">
                                <i class="material-icons-round">search</i>
                                <input type="text" class="form-control" name="searchbox" id="searchbox" placeholder="Cari kode, jenis, lokasi, atau pengguna..." autofocus>
                            </div>
                            <div>
                                <button id="openModalButton" class="btn btn-light mb-0" type="button">
                                    <i class="material-icons-round text-sm me-1">qr_code_scanner</i> Scan
                                </button>
                            </div>
                            @if (Auth::check() && (Auth::user()->status != 'Viewers' && Auth::user()->status != 'Auditor'))
                            <div class="ms-auto">
                                <a class="btn btn-primary mb-0" href="{{ route('add_inventory') }}">
                                    <i class="material-icons-round text-sm me-1">add</i> Tambah aset
                                </a>
                            </div>
                            @endif

                            <!-- The Modal -->
                            <div id="myModal" class="modal" aria-hidden="true">
                                <div class="modal-content">
                                    <span class="close">&times;</span>
                                    <h5>Scan kode aset</h5>
                                    <p class="text-sm text-secondary">Arahkan kamera ke QR code pada aset.</p>
                                    <div id="interactive" class="viewport"></div>
                                    <div id="result"></div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-shell">
                                <table id="inventoryTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kode Asset') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kategori Asset') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Asset Position') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Jenis') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Merk') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Description') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Perolehan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Nilai Perolehan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Nilai Saat Ini') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (hari)') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Dept') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Created at') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Barcode Availability') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center text-lg">
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
    <div class="modal fade" id="qrcodeModal" tabindex="-1" aria-labelledby="qrcodeModalLabel" aria-hidden="true" data-action-template="{{ route('process_qrcode', ['id' => '__asset__']) }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="qrcodeModalForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <div><span class="panel-kicker">Barcode</span><h5 class="modal-title" id="qrcodeModalLabel">Update asset barcode</h5></div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-sm text-secondary mb-3">Asset: <strong id="qrcodeAssetCode">-</strong></p>
                        <div class="form-group">
                            <label for="barcodeExists">Barcode availability</label>
                            <select class="form-select" id="barcodeExists" name="barcode_exists" required>
                                <option value="" selected disabled>Select an option</option>
                                <option value="yes">Available</option>
                                <option value="no">Not available</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="barcodeNote">Note</label>
                            <textarea class="form-control" id="barcodeNote" name="note" rows="3" placeholder="Optional note"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save barcode status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-plugins></x-plugins>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Initialize DataTable -->
    <script>
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        function tableText(value) {
            return $('<div>').text(value || '-').html();
        }

        function statusPill(value) {
            const status = String(value || '-');
            const statusClass = {
                'GOOD': 'good',
                'REPAIR': 'repair',
                'BREAKDOWN': 'breakdown',
                'WAITING DISPOSE': 'waiting',
                'DISPOSE': 'dispose'
            }[status.toUpperCase()] || 'neutral';

            return '<span class="table-status table-status-' + statusClass + '">' + tableText(status) + '</span>';
        }

        $(document).ready(function() {
            var table = $('#inventoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory') }}",
                columns: [{
                        data: 'asset_code',
                        name: 'asset_code',
                        render: function(data) {
                            return '<span class="table-code-cell"><i class="material-icons-round">qr_code_2</i>' + tableText(data) + '</span>';
                        }
                    },
                    {
                        data: 'asset_category',
                        name: 'asset_category',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'asset_position_dept',
                        name: 'asset_position_dept',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'asset_type',
                        name: 'asset_type',
                        render: function(data) {
                            return '<span class="table-primary-text">' + tableText(data ? data.toUpperCase() : '-') + '</span>';
                        }
                    },
                    {
                        data: 'merk',
                        name: 'merk',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'description',
                        name: 'description',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'serial_number',
                        name: 'serial_number',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'acquisition_date',
                        name: 'acquisition_date'
                    },
                    {
                        data: 'acquisition_value',
                        name: 'acquisition_value',
                        render: function(data) {
                            return data == 0 ? '-' : number_format(data, 0, ',', '.');
                        }
                    },
                    {
                        data: 'depreciated_value',
                        name: 'depreciated_value'
                    },
                    {
                        data: 'message',
                        name: 'message',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'location',
                        name: 'location',
                        render: function(data) {
                            return '<span class="table-location-cell"><i class="material-icons-round">location_on</i>' + tableText(data ? data.toUpperCase() : '-') + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return statusPill(data ? data.toUpperCase() : '-');
                        }
                    },
                    {
                        data: 'user',
                        name: 'user',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'dept',
                        name: 'dept',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        render: function(data, type, row) {
                            // Create a new Date object from the timestamp
                            const date = new Date(data);

                            // Format the date as needed (e.g., YYYY-MM-DD)
                            const formattedDate = date.toISOString().split('T')[0];

                            return formattedDate;
                        }
                    },
                    {
                        data: 'barcode_availability',
                        name: 'barcode_availability',
                        render: function(data) {
                            return '<span class="table-status table-status-available">' + tableText(data ? data.toUpperCase() : '-') + '</span>';
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                pageLength: 25,
                order: [
                    [15, 'desc']
                ],
                dom: '<"top">rt<"bottom"ip><"clear">',
                language: {
                    processing: "<div class='sima-loading'><span class='sima-spinner'></span><span>Memuat data aset...</span></div>",
                    emptyTable: "Belum ada data aset",
                    zeroRecords: "Aset yang dicari tidak ditemukan",
                    info: "Menampilkan _START_–_END_ dari _TOTAL_ aset",
                    infoEmpty: "Tidak ada aset untuk ditampilkan",
                    paginate: { previous: "Sebelumnya", next: "Berikutnya" }
                }
            });

            $('#searchbox').on('keyup', function() {
                table.search(this.value).draw();

                if (this.value.length >= 13) {
                    setTimeout(() => {
                        this.select();
                    }, 2000);
                }
            });
        });
    </script>
    <script>
        document.getElementById('qrcodeModal')?.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const modal = event.currentTarget;
            const form = document.getElementById('qrcodeModalForm');

            form.action = modal.dataset.actionTemplate.replace('__asset__', button.dataset.assetId);
            document.getElementById('qrcodeAssetCode').textContent = button.dataset.assetCode || '-';
            document.getElementById('barcodeExists').value = '';
            document.getElementById('barcodeNote').value = '';
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
