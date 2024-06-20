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
                            @if (Auth::check() && Auth::user()->status != 'Viewers')
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
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kode Asset Lama') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Kategori Asset') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Asset Position') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Jenis') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Description') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Serial') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal Perolehan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Nilai Perolehan') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Nilai Saat Ini') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Sisa Waktu Pakai (hari)') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Status') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('User') }}</th>
                                            @if (Auth::check() && (Auth::user()->status == 'Administrator' || Auth::user()->status == 'Modified' || Auth::user()->status == 'Super Admin'))
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($inventory as $inv)
                                        <tr class="text-center text-xxs">
                                            <td>{{ $inv->asset_code }}</td>
                                            <td>{{ $inv->old_asset_code != 0 ? $inv->old_asset_code : '-' }}</td>
                                            <td>{{ $inv->asset_category }}</td>
                                            <td>{{ $inv->asset_position_dept }}</td>
                                            <td>{{ $inv->asset_type }}</td>
                                            <td>{{ $inv->description }}</td>
                                            <td>{{ !empty($inv->serial_number) ? $inv->serial_number : '-' }}</td>
                                            <td>{{ $inv->acquisition_date }}</td>
                                            @if($inv->acquisition_value == 0)
                                            <td>-</td>
                                            @else
                                            <td>{{ number_format($inv->acquisition_value, 0, ',', '.') }}</td>
                                            @endif
                                            <?php
                                            if ($inv->acquisition_date === '-') {
                                                $message = "Tanggal tidak terdefinisi";
                                                $depreciatedValue = "-";
                                            } else {
                                                $acquisitionDate = new DateTime($inv->acquisition_date);
                                                $usefulLifeYears = $inv->useful_life;
                                                $currentDate = new DateTime();

                                                // Calculate the end date of the useful life directly
                                                $endOfUsefulLife = clone $acquisitionDate;
                                                $endOfUsefulLife->modify("+{$usefulLifeYears} years");

                                                $interval = $currentDate->diff($endOfUsefulLife);

                                                if ($currentDate > $endOfUsefulLife) {
                                                    $remainingDays = -$interval->days; // Use negative value for overdue days
                                                } else {
                                                    $remainingDays = $interval->days;
                                                }

                                                $message = "{$remainingDays} hari";

                                                // Output the message
                                                // echo $message;

                                                $usefulLifeYears = $inv->useful_life;
                                                $depreciationRate = 1 / $usefulLifeYears; // Calculate the depreciation rate

                                                // Initialize variables
                                                $acquisitionValue = $inv->acquisition_value;
                                                $yearsUsed = $acquisitionDate->diff($currentDate)->y;
                                                $depreciatedValue = $acquisitionValue;
                                                $accumulatedDepreciation = 0;

                                                for ($year = 1; $year <= $yearsUsed; $year++) {
                                                    $annualDepreciation = $depreciatedValue * $depreciationRate;
                                                    $accumulatedDepreciation += $annualDepreciation;
                                                    $depreciatedValue -= $annualDepreciation;

                                                    // Ensure depreciated value doesn't go below zero
                                                    if ($depreciatedValue < 0) {
                                                        $depreciatedValue = 0;
                                                        break;
                                                    }
                                                }

                                                // Ensure the values are appropriately handled when useful life is zero or not defined
                                                if ($usefulLifeYears == 0) {
                                                    $depreciatedValue = $acquisitionValue;
                                                }

                                                // Output the final depreciated value and accumulated depreciation
                                                // echo "Depreciated Value: " . $depreciatedValue . "\n";
                                                // echo "Accumulated Depreciation: " . $accumulatedDepreciation . "\n";
                                            }
                                            ?>
                                            @if($inv->acquisition_value == 0)
                                            <td>-</td>
                                            @else
                                            <td>{{ $depreciatedValueFormatted = $depreciatedValue === '-' ? '-' : number_format($depreciatedValue, 0, ',', '.'); }}</td>
                                            @endif
                                            <td>{{ $message }}</td>
                                            <td>{{ $inv->location }}</td>
                                            <td>{{ $inv->status }}</td>
                                            @if(isset($inv->user))
                                            <td>{{ $inv->user }}</td>
                                            @else
                                            <td>-</td>
                                            @endif
                                            @if (Auth::check() && Auth::user()->status == 'Administrator' || Auth::user()->status == 'Super Admin')
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div class="p-1">
                                                        <a href="{{ route('edit_inventory', ['id' => $inv->id]) }}" class="btn btn-success btn-sm p-0 mt-3" style="width: 24px; height: 24px;">
                                                            <i class="material-icons" style="font-size: 16px;">edit</i>
                                                        </a>
                                                    </div>
                                                    <div class="mx-1"></div>
                                                    <form action="{{ route('destroy_inventory', ['id' => $inv->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this asset?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm p-0 mt-3" style="width: 24px; height: 24px;">
                                                            <i class="material-icons" style="font-size: 16px;">close</i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endif
                                            @if (Auth::check() && Auth::user()->status == 'Modified')
                                            <td>
                                                <div class="d-flex align-items-center justify-content-center">
                                                    <div class="p-1">
                                                        <a href="{{ route('edit_inventory', ['id' => $inv->id]) }}" class="btn btn-success btn-sm p-0 mt-3" style="width: 24px; height: 24px;">
                                                            <i class="material-icons" style="font-size: 16px;">edit</i>
                                                        </a>
                                                    </div>
                                                    <div class="mx-1"></div>
                                                </div>
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
                        "targets": [0, 7]
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