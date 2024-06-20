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

    <x-navbars.sidebar activePage="dispose_inventory"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="DISPOSE ASSET"></x-navbars.navs.auth>
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
                            @if (Auth::check() && Auth::user()->status != 'Viewers')
                            <div class="ms-auto mb-2">
                                <a class="btn bg-gradient-dark mb-0" href="{{ route('input_dispose') }}">
                                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Dispose Asset
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
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Jenis') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (hari)') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Penghapusan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Dokumen') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status Approval') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Remarks') }}</th>
                                            @if (Auth::user()->hirar === "Manager" || Auth::user()->hirar === "Deputy General Manager")
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Approval') }}</th>
                                            @endif
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
                                            <td>
                                                @if ($item->disposal_document)
                                                <a href="{{ asset('storage/' . $item->disposal_document) }}" class="btn btn-sm mt-3 btn-secondary">Download Dokumen</a>
                                                @else
                                                -
                                                @endif
                                            </td>
                                            <td>{{ $item->approval ?? '-' }}</td>
                                            <td>{{ $item->note ?? '-' }}</td>
                                            @if (Auth::user()->hirar === "Manager" || Auth::user()->hirar === "Deputy General Manager")
                                            <td>
                                                <form action="{{ route('approval') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="itemId" value="{{ $item->id }}">
                                                    <input type="hidden" name="itemId2" value="{{ $item->asset_code }}">
                                                    <input type="hidden" name="hirar" value="{{ Auth::user()->hirar }}">

                                                    @if(Auth::user()->hirar == 'Manager')
                                                    <!-- Tombol Approve -->
                                                    <button type="submit" name="approval_action" value="Approve" class="btn btn-success btn-sm mt-3" title="Approve" @if ($item->approval === 'Approve by Deputy General Manager' || $item->approval === 'Reject by Deputy General Manager')
                                                        disabled
                                                        @endif
                                                        >
                                                        <i class="fas fa-check"></i>
                                                    </button>

                                                    <!-- Tombol Reject -->
                                                    <button type="submit" name="approval_action" value="Reject" class="btn btn-danger btn-sm mt-3" title="Reject" @if ($item->approval === 'Approve by Deputy General Manager' || $item->approval === 'Reject by Deputy General Manager')
                                                        disabled
                                                        @endif
                                                        >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @elseif(Auth::user()->hirar == 'Deputy General Manager')
                                                    <!-- Tombol Approve -->
                                                    <button type="submit" name="approval_action" value="Approve" class="btn btn-success btn-sm mt-3" title="Approve" @if ($item->approval === 'Pending')
                                                        disabled
                                                        @endif
                                                        >
                                                        <i class="fas fa-check"></i>
                                                    </button>

                                                    <!-- Tombol Reject -->
                                                    <button type="submit" name="approval_action" value="Reject" class="btn btn-danger btn-sm mt-3" title="Reject" @if ($item->approval === 'Pending')
                                                        disabled
                                                        @endif
                                                        >
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                </form>
                                            </td>
                                            @endif
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
    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            var table = $('#inventoryTable').DataTable({
                "pageLength": 50,
                "columnDefs": [{
                        "orderable": true,
                        "targets": 6
                    }, // Enable ordering on the 8th column (index 7)
                    {
                        "orderable": false,
                        "targets": '_all'
                    } // Disable ordering on all other columns
                ],
                "order": [
                    [6, 'desc']
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