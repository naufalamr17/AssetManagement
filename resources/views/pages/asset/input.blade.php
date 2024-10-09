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

    <x-navbars.sidebar activePage="inventory"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="INPUT ASSET"></x-navbars.navs.auth>
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

                        <div class="d-flex flex-wrap align-items-center mb-4 p-3">
                            <div class="mb-2 me-2">
                                <input type="text" class="form-control border p-2" name="searchbox" id="searchbox" placeholder="Search..." style="max-width: 300px;" autofocus>
                            </div>
                            <div class="mb-2 me-2 mt-3">
                                <button id="openModalButton" class="btn btn-danger">
                                    <i class="fas fa-camera"></i>
                                </button>
                            </div>
                            @if (Auth::check() && (Auth::user()->status != 'Viewers' && Auth::user()->status != 'Auditor'))
                            <div class="ms-auto mb-2">
                                <a class="btn bg-gradient-dark mb-0" href="{{ route('add_inventory') }}">
                                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add Asset
                                </a>
                            </div>
                            @endif

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
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Merk') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Description') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Perolehan') }}</th>
                                            @if (Auth::check() && (Auth::user()->location != 'Site Molore' && Auth::user()->location != 'Office Kendari'))
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Nilai Perolehan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Nilai Saat Ini') }}</th>
                                            @endif
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (hari)') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Dept') }}</th>
                                            @if (Auth::check() && (Auth::user()->status == 'Administrator' || Auth::user()->status == 'Modified' || Auth::user()->status == 'Super Admin'))
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Action') }}</th>
                                            @endif
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

        $(document).ready(function() {
            var table = $('#inventoryTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('inventory') }}",
                columns: [{
                        data: 'asset_code',
                        name: 'asset_code'
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
                            return data ? data.toUpperCase() : '-';
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
                    @if(Auth::check() && (Auth::user() -> location != 'Site Molore' && Auth::user() -> location != 'Office Kendari')) {
                        data: 'acquisition_value',
                        name: 'acquisition_value',
                        render: function(data) {
                            return data == 0 ? '-' : number_format(data, 0, ',', '.');
                        }
                    },
                    {
                        data: 'depreciated_value',
                        name: 'depreciated_value',
                        render: function(data) {
                            return data == 0 ? '-' : number_format(data, 0, ',', '.');
                        }
                    },
                    @endif {
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
                            return data ? data.toUpperCase() : '-';
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            return data ? data.toUpperCase() : '-';
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
                    @if(Auth::check() && (Auth::user() -> status == 'Administrator' || Auth::user() -> status == 'Super Admin' || Auth::user() -> status == 'Modified')) {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                    @endif
                ],
                pageLength: 50,
                order: [
                    [8, 'desc']
                ],
                dom: '<"top">rt<"bottom"ip><"clear">',
                language: {
                    processing: "<div class='d-flex justify-content-center align-items-center' style='position: fixed; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); top: 0; left: 0; z-index: 1000;'>" +
                        "<div class='spinner-border' role='status'>" +
                        "<span class='sr-only'>Loading...</span>" +
                        "</div>" +
                        "</div>"
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