<x-layout bodyClass="g-sidenav-show  bg-gray-200">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <!-- Custom CSS to make the DataTable smaller -->
    <style>
        #letterTable_wrapper .dataTables_length,
        #letterTable_wrapper .dataTables_filter,
        #letterTable_wrapper .dataTables_info,
        #letterTable_wrapper .dataTables_paginate {
            font-size: 0.75rem;
        }

        #letterTable {
            font-size: 0.75rem;
        }

        #letterTable th,
        #letterTable td {
            padding: 4px 8px;
        }

        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
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
            max-width: 500px;
            border-radius: 8px;
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

        .form-control {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        @media only screen and (max-width: 600px) {
            .modal-content {
                width: 90%;
                max-width: none;
                height: 60vh;
                overflow-y: auto;
            }
        }
    </style>

    <x-navbars.sidebar activePage="generate-letter"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="GENERATE LETTER"></x-navbars.navs.auth>
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
                            <div class="ms-auto mb-2">
                                @if(Auth::check() && Auth::user()->status != 'Viewers')
                                <button id="addDataButton" class="btn bg-gradient-danger">
                                    <i class="material-icons text-sm">add</i>&nbsp;&nbsp;Add Data
                                </button>
                                @endif
                                <button id="exportExcelButton" class="btn bg-gradient-dark">
                                    <i class="material-icons text-sm">file_download</i>&nbsp;&nbsp;Download Excel
                                </button>
                            </div>
                        </div>

                        <!-- The Modal -->
                        <div id="addDataModal" class="modal">
                            <div class="modal-content">
                                <span class="close"></span>
                                <h4 id="modalTitle">Add Data</h4>
                                <form id="addDataForm" method="POST">
                                    @csrf
                                    <input type="hidden" id="letterId" name="letterId">
                                    <div class="mb-3">
                                        <label for="tanggal" class="form-label">Tanggal</label>
                                        <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jenisBA" class="form-label">Jenis BA</label>
                                        <select class="form-control" id="jenisBA" name="jenisBA" required>
                                            <option value="ASSET SERAH TERIMA">ASSET SERAH TERIMA</option>
                                            <option value="ASSET HILANG">ASSET HILANG</option>
                                            <option value="FORM KERUSAKAN ASSET">FORM KERUSAKAN ASSET</option>
                                            <option value="BAST">BAST</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="perihal" class="form-label">Perihal</label>
                                        <select class="form-control" id="perihal" name="perihal" required>
                                            <option value="PEMINJAMAN ASSET">PEMINJAMAN ASSET</option>
                                            <option value="PENGEMBALIAN ASSET">PENGEMBALIAN ASSET</option>
                                            <option value="MUTASI ASSET">MUTASI ASSET</option>
                                        </select>
                                        <input type="hidden" id="hiddenPerihal" name="hiddenPerihal" value="-">
                                    </div>
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                </form>
                            </div>
                        </div>

                        <!-- Add Document Modal -->
                        <div id="addDocumentModal" class="modal">
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <h4 id="modalTitle">Add Document</h4>
                                <form action="{{ route('letters.addDocument') }}" id="addDocumentForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="letter_id" id="letter_id">
                                    <div class="form-group">
                                        <label for="file">Upload Document</label>
                                        <input type="file" class="form-control" id="file" name="file" accept="application/pdf" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>

                        <div class="card-body px-2 pb-2">
                            <div class="table-responsive p-0">
                                <table id="letterTable" class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Tanggal') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('No Surat') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Perihal') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Creator') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Location') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('File') }}</th>
                                            <th class="text-center text-secondary text-xxs font-weight-bolder opacity-7">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Add your dynamic rows here -->
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
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.colVis.min.js"></script>

    <!-- Initialize DataTable -->
    <script>
        $(document).ready(function() {
            var table = $('#letterTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('generate-letter') }}",
                columns: [{
                        data: 'tanggal',
                        name: 'tanggal'
                    },
                    {
                        data: 'kode_surat',
                        name: 'kode_surat'
                    },
                    {
                        data: 'perihal',
                        name: 'perihal'
                    },
                    {
                        data: 'creator',
                        name: 'creator'
                    },
                    {
                        data: 'location',
                        name: 'location'
                    },
                    {
                        data: 'file',
                        name: 'file'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                dom: '<"top">rt<"bottom"ip><"clear">',
                createdRow: function(row, data, dataIndex) {
                    $(row).addClass('text-center').css('font-size', '14px');
                }
            });

            // Add the search functionality
            $('#searchbox').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Export to Excel functionality
            $('#exportExcelButton').on('click', function() {
                const sheetName = 'Report';
                const fileName = 'report_letters';

                const table = document.getElementById('letterTable');

                if (!table) {
                    console.error('Tabel tidak ditemukan.');
                    return;
                }

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);

                XLSX.utils.book_append_sheet(wb, ws, sheetName);
                XLSX.writeFile(wb, fileName + '.xlsx');
            });

            // Add Data Modal functionality
            var modal = document.getElementById('addDataModal');
            var btn = document.getElementById('addDataButton');
            var span = document.getElementsByClassName('close')[0];

            btn.onclick = function() {
                modal.style.display = "block";
                $('#addDataForm').trigger("reset");
                $('#letterId').val('');
                $('#modalTitle').text('Add Data');
                $('#addDataForm').attr('action', "{{ route('letters.store') }}");
                $('#addDataForm').attr('method', 'POST');
                $('#tanggal').prop('readonly', false);
            }

            span.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }

            // Enable or disable "Perihal" based on "Jenis BA"
            $('#jenisBA').on('change', function() {
                var jenisBA = $(this).val();
                var perihal = $('#perihal');
                var hiddenPerihal = $('#hiddenPerihal');

                if (jenisBA === 'ASSET SERAH TERIMA') {
                    perihal.html(`
                        <option value="PEMINJAMAN ASSET">PEMINJAMAN ASSET</option>
                        <option value="PENGEMBALIAN ASSET">PENGEMBALIAN ASSET</option>
                        <option value="MUTASI ASSET">MUTASI ASSET</option>
                    `);
                    perihal.prop('disabled', false);
                    hiddenPerihal.val('');
                } else if (jenisBA === 'FORM KERUSAKAN ASSET') {
                    perihal.html(`
                        <option value="PENGGANTIAN ASSET">PENGGANTIAN ASSET</option>
                        <option value="PERBAIKAN ASSET">PERBAIKAN ASSET</option>
                    `);
                    perihal.prop('disabled', false);
                    hiddenPerihal.val('');
                } else if (jenisBA === 'BAST') {
                    perihal.html(`
                        <option value="Radio">Radio</option>
                        <option value="General">General</option>
                    `);
                    perihal.prop('disabled', false);
                    hiddenPerihal.val('');
                } else {
                    perihal.html(`
                        <option value="-" selected>-</option>
                    `);
                    perihal.prop('disabled', true);
                    hiddenPerihal.val('-');
                }
            }).trigger('change'); // Trigger change event on page load to set initial state

            // Update hidden input value on form submit
            $('#addDataForm').on('submit', function() {
                if ($('#perihal').prop('disabled')) {
                    $('#hiddenPerihal').val($('#perihal').val());
                }
            });

            // Handle add document button click
            $(document).on('click', '.add-document', function() {
                var letterId = $(this).data('id');
                $('#letter_id').val(letterId);
                $('#addDocumentModal').show();
            });

            // Handle modal close
            $('.close').on('click', function() {
                $('.modal').hide();
            });

            // Edit button functionality
            $('body').on('click', '.edit', function() {
                var data = table.row($(this).parents('tr')).data();
                $('#letterId').val(data.id);
                $('#tanggal').val(data.tanggal).prop('readonly', true);
                $('#jenisBA').val(data.jenisBA).trigger('change');
                $('#perihal').val(data.perihal);
                $('#modalTitle').text('Edit Data');
                $('#addDataForm').attr('action', "{{ route('letters.update', '') }}/" + data.id);
                $('#addDataForm').attr('method', 'POST');
                $('<input>').attr({
                    type: 'hidden',
                    name: '_method',
                    value: 'PUT'
                }).appendTo('#addDataForm');
                modal.style.display = "block";
            });

            // Delete button functionality
            $('body').on('click', '.delete', function() {
                var data = table.row($(this).parents('tr')).data();
                if (confirm("Are you sure you want to delete this record?")) {
                    $.ajax({
                        url: "{{ route('letters.destroy', '') }}/" + data.id,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.ajax.reload();
                            alert(response.success);
                        }
                    });
                }
            });
        });
    </script>
</x-layout>