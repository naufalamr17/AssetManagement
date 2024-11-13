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
            max-height: 400px;
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
                                            @if (Auth::user()->hirar === "Manager" || Auth::user()->hirar === "Deputy General Manager" || Auth::user()->hirar === "Supervisor")
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Approval') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventory as $item)
                                        <tr class="text-center" style="font-size: 14px;">
                                            <td>{{ strtoupper($item->asset_code ?? '-') }}</td>
                                            <td>{{ strtoupper($item->asset_type ?? '-') }}</td>
                                            <td>{{ strtoupper($item->serial_number ?? '-') }}</td>
                                            <?php
                                            if ($item->acquisition_date === '-') {
                                                $message = "TANGGAL TIDAK TERDEFINISI";
                                            } else {
                                                $acquisitionDate = new DateTime($item->acquisition_date);
                                                $usefulLife = $item->useful_life * 365; // Ubah umur manfaat dari tahun ke hari
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
                                            <td>{{ strtoupper($item->location ?? '-') }}</td>
                                            <td>{{ strtoupper($item->status ?? '-') }}</td>
                                            <td>{{ strtoupper($item->tanggal_penghapusan ?? '-') }}</td>
                                            <td>
                                                @if ($item->disposal_document)
                                                <!-- Tampilkan tombol download jika disposal_document ada -->
                                                <a href="{{ asset('storage/' . $item->disposal_document) }}" class="btn btn-sm mt-3 btn-secondary">DOWNLOAD DOKUMEN</a>
                                                @elseif (strtoupper($item->approval) === 'APPROVE BY DEPUTY GENERAL MANAGER' || strtoupper($item->approval) === 'APPROVE BY MANAGER' || strtoupper($item->approval) === 'APPROVE BY SUPERVISOR')
                                                <!-- Jika approval adalah 'Approve by Deputy General Manager', tampilkan tombol 'Add Document' -->
                                                <a href="{{ route('add.document', $item->id) }}" class="btn btn-sm mt-3 btn-primary">ADD DOCUMENT</a>
                                                @else
                                                <!-- Jika tidak ada document dan approval bukan 'Approve by Deputy General Manager', tampilkan '-' -->
                                                -
                                                @endif
                                            </td>
                                            <td>{{ strtoupper($item->approval ?? '-') }}</td>
                                            <td>{{ strtoupper($item->note ?? '-') }}</td>
                                            @if (Auth::user()->hirar === "Manager" || Auth::user()->hirar === "Deputy General Manager" || Auth::user()->hirar === "Supervisor")
                                            <td>
                                                <form action="{{ route('approval') }}" method="post">
                                                    @csrf
                                                    <input type="hidden" name="itemId" value="{{ $item->id }}">
                                                    <input type="hidden" name="itemId2" value="{{ strtoupper($item->asset_code) }}">
                                                    <input type="hidden" name="hirar" value="{{ Auth::user()->hirar }}">

                                                    @if(strtoupper(Auth::user()->hirar) == 'SUPERVISOR')
                                                    <!-- Approval button -->
                                                    <button type="submit" name="approval_action" value="APPROVE" class="btn btn-success btn-sm mt-3" title="APPROVE" @if (strtoupper($item->approval) === 'APPROVE BY MANAGER' || strtoupper($item->approval) === 'REJECT BY MANAGER' || strtoupper($item->approval) === 'APPROVE BY DEPUTY GENERAL MANAGER' || $item->approval === 'REJECT BY DEPUTY GENERAL MANAGER') disabled @endif>
                                                        <i class="fas fa-check"></i>
                                                    </button>

                                                    <!-- Rejection button -->
                                                    <button type="submit" name="approval_action" value="REJECT" class="btn btn-danger btn-sm mt-3" title="REJECT" @if (strtoupper($item->approval) === 'APPROVE BY MANAGER' || strtoupper($item->approval) === 'REJECT BY MANAGER' || strtoupper($item->approval) === 'APPROVE BY DEPUTY GENERAL MANAGER' || $item->approval === 'REJECT BY DEPUTY GENERAL MANAGER') disabled @endif>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @elseif(strtoupper(Auth::user()->hirar) == 'MANAGER')
                                                    <!-- Approval button -->
                                                    <button type="submit" name="approval_action" value="APPROVE" class="btn btn-success btn-sm mt-3" title="APPROVE" @if (strtoupper($item->approval) === 'APPROVE BY DEPUTY GENERAL MANAGER' || strtoupper($item->approval) === 'REJECT BY DEPUTY GENERAL MANAGER' || strtoupper($item->approval) === 'PENDING') disabled @endif>
                                                        <i class="fas fa-check"></i>
                                                    </button>

                                                    <!-- Rejection button -->
                                                    <button type="submit" name="approval_action" value="REJECT" class="btn btn-danger btn-sm mt-3" title="REJECT" @if (strtoupper($item->approval) === 'APPROVE BY DEPUTY GENERAL MANAGER' || strtoupper($item->approval) === 'REJECT BY DEPUTY GENERAL MANAGER' || strtoupper($item->approval) === 'PENDING') disabled @endif>
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @elseif(strtoupper(Auth::user()->hirar) == 'DEPUTY GENERAL MANAGER')
                                                    <!-- Approval button -->
                                                    <button type="submit" name="approval_action" value="APPROVE" class="btn btn-success btn-sm mt-3" title="APPROVE" @if (strtoupper($item->approval) === 'APPROVE BY SUPERVISOR' || strtoupper($item->approval) === 'REJECT BY SUPERVISOR' || strtoupper($item->approval) === 'PENDING') disabled @endif>
                                                        <i class="fas fa-check"></i>
                                                    </button>

                                                    <!-- Rejection button -->
                                                    <button type="submit" name="approval_action" value="REJECT" class="btn btn-danger btn-sm mt-3" title="REJECT" @if (strtoupper($item->approval) === 'APPROVE BY SUPERVISOR' || strtoupper($item->approval) === 'REJECT BY SUPERVISOR' || strtoupper($item->approval) === 'PENDING') disabled @endif>
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